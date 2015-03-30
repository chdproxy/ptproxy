<?php

class PeerController extends Controller {

  public function accessRules() {
    return array(
      array(
        'allow',
        'actions' => array('index', 'import', 'trash', 'delete', 'check'),
        'roles' => array('administrator', 'editor'),
      ),
      array(
        'deny',
        'users' => array('*'),
      ),
    );
  }

  public function filters() {
    return array(
      'accessControl',
    );
  }

  public function actionIndex() {
  }

  public function actionImport() {
    $info_hash = Yii::app()->request->getParam('info_hash');
    if (strlen($info_hash) != 40) {
      throw new CHttpException(403, 'info_hash length error');
    }
    if (Yii::app()->request->getIsPostRequest()) {
      $peers = Yii::app()->request->getPost('peers');
      if ($peers == NULL) {
        throw new CHttpException(403, 'missing params');
      }
      $info_hash = hex2bin($info_hash);
      $hack_torrent = HackTorrent::model()->findByPk($info_hash);
      if (!$hack_torrent) {
        throw new CHttpException(403, 'torrent not registered by tracker');
      }
      $site = $hack_torrent->site;
      unset($hack_torrent);
      $peers = preg_split('/[\r\n]+/', $peers, -1, PREG_SPLIT_NO_EMPTY);
      if (isset($peers[0])) {
        foreach ($peers as $peer) {
          list($ip, $port) = explode(':', $peer, 2);
          if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) and (intval($port) < 0xffff) and (intval($port) > 0)) {
            $newPeer = HackPeer::model()->findByAttributes(array(
              'info_hash' => $info_hash,
              'ip' => $ip,
              'port' => intval($port)
            ));
            if (!$newPeer) {
              $newPeer = new HackPeer();
            }
            $newPeer->info_hash = $info_hash;
            $newPeer->ip = $ip;
            $newPeer->port = intval($port);
            $newPeer->site = $site;
            $newPeer->updated_at = time();
            $newPeer->save();
            unset($newPeer);
          }
        }
        Yii::app()->db->createCommand('DELETE FROM hack_peer WHERE site=:site AND updated_at < :expr')
          ->execute(array('site' => intval($site), 'expr' => time() - $this->module->auto_peer_expr));
        $this->redirect(Yii::app()->createAbsoluteUrl('pt2bt/default/list'));
      }
      else {
        throw new CHttpException(403, 'empty peers');
      }
      Yii::app()->end();
    }
    $pn = 0;
    $peerN = Yii::app()->db->createCommand('SELECT count(*) AS PN FROM hack_peer WHERE info_hash=:info_hash')
      ->queryRow(TRUE, array('info_hash' => hex2bin($info_hash)));
    if ($peerN) {
      $pn = $peerN['PN'] + 0;
    }
    $this->render('import', array('info_hash' => $info_hash, 'peerNums' => $pn));
  }

  public function actionTrash() {
    if (Yii::app()->request->getIsPostRequest()) {
      $site = Yii::app()->request->getPost('site');
      if (!is_numeric($site) or $site < 0) {
        throw new CHttpException(403);
      }
      Yii::app()->db->createCommand('DELETE FROM hack_peer WHERE site=:site')->bindValue('site', $site)->query();
    }
    $this->render('trash');
  }

  public function actionDelete() {
    if (Yii::app()->request->getIsPostRequest()) {
      $info_hash = Yii::app()->request->getPost('info_hash');
      if (strlen($info_hash) != 40) {
        throw new CHttpException(403);
      }
      Yii::app()->db->createCommand('DELETE FROM hack_peer WHERE info_hash=:info_hash')
        ->execute(array('info_hash' => hex2bin($info_hash)));
      $this->redirect(Yii::app()->createAbsoluteUrl('pt2bt/peer/import', array('info_hash' => $info_hash)));
    }
  }

  public function actionCheck() {
    $info_hash = Yii::app()->request->getQuery('info_hash');
    $seeder = Yii::app()->request->getQuery('seeder', 1) + 0;
    if ($seeder == 1) {
      $results = Yii::app()->db->createCommand('SELECT * FROM snatch WHERE info_hash=:info_hash AND `left`=0')
        ->queryAll(TRUE, array('info_hash' => hex2bin($info_hash)));
    }
    elseif ($seeder == 0) {
      $results = Yii::app()->db->createCommand('SELECT * FROM snatch WHERE info_hash=:info_hash AND `left`>0')
        ->queryAll(TRUE, array('info_hash' => hex2bin($info_hash)));
    }
    else {
      $results = Yii::app()->db->createCommand('SELECT * FROM snatch WHERE info_hash=:info_hash')
        ->queryAll(TRUE, array('info_hash' => hex2bin($info_hash)));
    }

    foreach ($results as $peer) {
      echo urlencode($peer['peer_id']) . " " . inet_ntop($peer['ip']) . " " . $peer['port'] . "\n";
    }
  }
}