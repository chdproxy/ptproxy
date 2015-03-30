<?php

class CronController extends Controller {

  private $S_KEY =  'CRON_KEY_HERE';//TODO 修改你的cron job 密码

  public function actionIndex() {
  }

  public function actionFixSeederLeacher() {
    if ($this->S_KEY != Yii::app()->request->getQuery('key')) {
      echo 'ERROR';
      Yii::app()->end();
    }
    Yii::app()->db->createCommand('DELETE FROM `snatch` WHERE `last_access` < ' . (time() - 7200))->execute();
    $fixTorrents = Yii::app()->db->createCommand('SELECT info_hash AS IH,COUNT(IF(`left`!=0,TRUE,NULL)) AS LC,COUNT(*) AS TT FROM snatch GROUP BY info_hash ORDER BY TT DESC')
      ->queryAll();
    if ($fixTorrents) {
//        var_dump($fixTorrents);
      foreach ($fixTorrents as $torrent) {
        echo bin2hex($torrent['IH']) . "\t" . ($torrent['TT'] - $torrent['LC']) . "\t" . $torrent['LC'] . "\t" . "\n";
        Yii::app()->db->createCommand('UPDATE hack_torrent SET seeder=:seeder , leacher=:leacher WHERE info_hash=:info_hash')
          ->execute(array(
            'seeder' => intval($torrent['TT'] - $torrent['LC']),
            'leacher' => $torrent['LC'] + 0,
            'info_hash' => $torrent['IH']
          ));
      }
    }
    Yii::app()->db->createCommand('UPDATE hack_torrent SET status=0 WHERE status=1 AND last_active < (unix_timestamp()-3600*24*7)')
      ->execute();
  }

}