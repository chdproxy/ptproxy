<?php

class CronJobController extends Controller {

  private $S_KEY =  'CRON_KEY_HERE';//TODO 修改你的cron job 密码

  public function actionIndex() {
  }

  public function actionDeleteOldTorrent() {
    if ($this->S_KEY != Yii::app()->request->getQuery('key')) {
      echo 'ERROR';
      Yii::app()->end();
    }
    $torrents = Yii::app()->db->createCommand('SELECT info_hash FROM hack_torrent WHERE status=0 AND last_active < ' . (time() - 3600 * 24 * 30))
      ->queryAll();
    if ($torrents) {
//        var_dump($fixTorrents);
      foreach ($torrents as $torrent) {
        Yii::app()->db->createCommand('DELETE FROM hack_torrent WHERE info_hash=:info_hash')
          ->execute(array('info_hash' => $torrent['info_hash']));
        $torrent_path = file_get_contents($this->module->torrent_save_path . '/' . bin2hex($torrent['info_hash']));
//        if(file_exists($torrent_path)){
        unlink($torrent_path);
//        }
      }
    }
  }

}