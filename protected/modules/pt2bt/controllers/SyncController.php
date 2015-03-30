<?php

class SyncController extends Controller {

  private $S_KEY = 'CRON_KEY_HERE';//TODO 修改你的cron job 密码

  public function actionIndex() {
  }

  public function actionAdd() {
    if (Yii::app()->request->getIsPostRequest()) {
      if ($this->S_KEY != Yii::app()->request->getPost('key')) {
        throw new CHttpException(403, 'access deny');
      }
      $info_hash = Yii::app()->request->getPost('info_hash');
      if (strlen($info_hash) != 40) {
        throw new CHttpException(403, 'info_hash length error');
      }
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
        return CJSON::encode(array('result' => TRUE, 'msg' => 'success import'));
      }
      else {
        throw new CHttpException(403, 'empty peers or site');
      }
    }
    return CJSON::encode(array('result' => FALSE, 'msg' => 'un'));
  }

}