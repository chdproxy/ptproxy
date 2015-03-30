<?php

class TrackerController extends CController {

  public $defaultAction = 'scrape';

  private function resp_error($reason) {
    header("Content-Type: text/plain; charset=utf-8");
    header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    header("Pragma: no-cache");
    echo Bencode::encode(array(
//      'failure reason' => $reason,
      'failure reason' => 'access deny',
      'interval' => 1800,
      'min interval' => 60
    ));
    Yii::app()->end();
  }

  private function resp_success($p, $complete = 0, $incomplete = 0) {
    header("Content-Type: text/plain; charset=utf-8");
    header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    header("Pragma: no-cache");
    echo Bencode::encode(array(
      'complete' => $complete + 0,
      'incomplete' => $incomplete + 0,
      'interval' => 600 + rand(600, 1800),
      'min interval' => 60,
      'peers' => $p,
    ));
    Yii::app()->end();
  }

  private function getRealIp() {
    $ip = FALSE;
    if (isset($_SERVER)) {
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }
      elseif (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      }
      else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
    }
    else {
      if (getenv('HTTP_X_FORWARDED_FOR') && filter_var(getenv('HTTP_X_FORWARDED_FOR'), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
      }
      elseif (getenv('HTTP_CLIENT_IP') && filter_var(getenv('HTTP_CLIENT_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $ip = getenv('HTTP_CLIENT_IP');
      }
      else {
        $ip = getenv('REMOTE_ADDR');
      }
    }
    return $ip;
  }

  public function actionAnnounce() {
    if (Yii::app()->request->serverPort != $this->module->hack_tracker_port) {
      Yii::app()->end();
    }
    foreach (array('info_hash', 'peer_id', 'uploaded', 'downloaded', 'left', 'port') as $reqparam) {
      ${$reqparam} = Yii::app()->request->getQuery($reqparam);
      if (${$reqparam} === NULL) {
        self::resp_error('missing query key=' . $reqparam);
      }
    }

    if (strlen($info_hash) != 20 or strlen($peer_id) != 20) {
      self::resp_error('info_hash or peer_id must be 20 bytes length');
    }
    $compact = intval(Yii::app()->request->getQuery('compact', 1));
    $numwant = Yii::app()->request->getQuery('numwant', 0);

//    $userAgent = Yii::app()->request->getUserAgent(); //check user agent

    foreach (array('port', 'uploaded', 'downloaded', 'left', 'numwant', 'compact') as $nb) { //numeric check
      if (!is_numeric(${$nb})) {
        self::resp_error('numeric required key=' . $nb);
      }
    }

    $event = Yii::app()->request->getQuery('event');
    if (!in_array($event, array(NULL, 'started', 'stopped', 'completed'))) {
      self::resp_error('invalid event');
    }

    $numwant = min(500, max($numwant, 100)) + 0;

    foreach (array('port', 'uploaded', 'downloaded', 'left') as $p) {
      ${$p} = ${$p} + 0;
      if (${$p} < 0) {
        self::resp_error('invalid value key=' . $p);
      }
    }

    if (!$port || $port > 0xffff) { //TODO xunlei submit fake port
      self::resp_error('invalid value key=port');
    }

    $ip = self::getRealIp();
    if ($ip === FALSE) {
      self::resp_error('invalid IP address');
    }
//END params check


    $hack_torrent = HackTorrent::model()->findByAttributes(array('info_hash' => $info_hash));
    if (!$hack_torrent) { //没有注册种子，直接退出
      $this->resp_error('torrent not registered by this tracker');
    }
    if (!$hack_torrent->status) {
      $this->resp_error('torrent disabled by staff');
    }

    $current_request_time = time();

    $snatch = Snatch::model()->findByAttributes(array('peer_id' => $peer_id, 'info_hash' => $info_hash));
    if (!$snatch) {
      $snatch = new Snatch();
      $snatch->created = $current_request_time;
      $snatch->peer_id = $peer_id;
      $snatch->info_hash = $info_hash;
    }
    $upload_diff = $uploaded - $snatch->uploaded;
    $download_diff = $downloaded - $snatch->downloaded;
    if ($upload_diff >= 0 && $download_diff >= 0) {
      $hack_torrent->uploaded += $upload_diff;
      $hack_torrent->downloaded += $download_diff;
    }
    $snatch->uploaded = $uploaded;
    $snatch->downloaded = $downloaded;
    $snatch->left = $left;
    $snatch->ip = inet_pton($ip);
    $snatch->port = $port;
    $snatch->last_access = $current_request_time;
    $snatch->save();

    //清理过期peer,两小时ttl
    /*    if (rand(1, 2000) < 2) {
          Yii::app()->db->createCommand('DELETE FROM `snatch` WHERE `last_access` < ' . ($current_request_time - 7200))
            ->execute();
        }*/

    $hack_torrent->last_active = $current_request_time;

    if ($event == 'completed' && $left == 0) {
      $hack_torrent->completed += 1;
      $hack_torrent->seeder += 1;
      $hack_torrent->leacher = max(0, $hack_torrent->leacher - 1);
      $hack_torrent->save();
      //TODO 返回未完成用户，PT2BT原理不需要该功能，由公共TRACKER处理
      $this->resp_success('');
    }

    if ($event == 'started') {
      if ($left > 0) { //TODO
        $hack_torrent->leacher += 1;
        $hack_torrent->save();
        $peer_cached = Yii::app()->cache->get('p_' . $info_hash);
        if ($peer_cached) {
          self::resp_success($peer_cached, $hack_torrent->seeder, $hack_torrent->leacher); //缓存读取，30秒缓存，减小数据库读取，磁盘负荷
        }
        $this->resp_success($this->getHackPeer($info_hash, $hack_torrent->site, $numwant), $hack_torrent->seeder, $hack_torrent->leacher);
      }
      //TODO 返回未完成用户，PT2BT原理不需要该功能，由公共TRACKER处理
      $this->resp_success('');
    }

    if ($event == 'stopped') {
      if ($left > 0) {
        $hack_torrent->leacher = max(0, $hack_torrent->leacher - 1);
      }
      else {
        $hack_torrent->seeder = max(0, $hack_torrent->seeder - 1);
      }
      $hack_torrent->save();
//      $snatch->delete();
      $this->resp_success('');
    }

    //handel scrape
    $hack_torrent->save();
    if ($left == 0) {
      $this->resp_success('', $hack_torrent->seeder, $hack_torrent->leacher);
      Yii::app()->end();
    }
    $peer_cached = Yii::app()->cache->get('p_' . $info_hash);
    if ($peer_cached) {
      self::resp_success($peer_cached, $hack_torrent->seeder, $hack_torrent->leacher); //缓存读取，30秒缓存，减小数据库读取，磁盘负荷
    }
    $this->resp_success($this->getHackPeer($info_hash, $hack_torrent->site, $numwant), $hack_torrent->seeder, $hack_torrent->leacher);
  }

  private function getHackPeer($info_hash, $site, $limit) {
//    //TODO
    $cached_orign = Yii::app()->cache->get('cached_orign_' . $info_hash);
    if ($cached_orign) {
      return $cached_orign;
    }
    elseif (file_exists(Yii::app()->basePath . '/../htdocs/cachedpeer/' . bin2hex($info_hash))) {
      $annfile = file_get_contents(Yii::app()->basePath . '/../htdocs/cachedpeer/' . bin2hex($info_hash), FILE_BINARY);
      if ($annfile) {
        try {
          $cached_orign = Bencode::decode($annfile);
          if (!empty($cached_orign['peers'])) {
            $cached_orign = $cached_orign['peers'];
            Yii::app()->cache->set('cached_orign_' . $info_hash, $cached_orign, 600);
            return $cached_orign;
          }
        } catch (Exception $e) {

        }
      }
    }
    ///
    $peers = HackPeer::model()
      ->findAllBySql('SELECT ip,port FROM hack_peer WHERE info_hash=:info_hash ORDER BY RAND() LIMIT :limit', array(
        'info_hash' => $info_hash,
        'limit' => $limit + rand(300, 800)
      ));
    if (!$peers) { // 如果没有对应种子专有PEER，就随机选择该站任意PEER
      $peers = HackPeer::model()
        ->findAllBySql('SELECT ip,port FROM hack_peer WHERE site=:site ORDER BY RAND() LIMIT :limit', array(
          'site' => $site,
          'limit' => $limit + rand(300, 800)
        ));
    }
    if ($peers) {
      shuffle($peers);
      $p = '';
      foreach ($peers as $peer) {
        if (ip2long($peer->ip)) {
          $p .= pack('Nn', ip2long($peer->ip), intval($peer->port));
        }
        unset($peer);
      }
      Yii::app()->cache->set('p_' . $info_hash, $p, 30);
      return $p;
    }
    return '';
  }

  public function actionScrape() {
//    //TODO scrape功能
    Yii::app()->end();
  }
}
