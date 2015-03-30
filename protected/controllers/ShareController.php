<?php

class ShareController extends CController {

  private $S_KEY = 'CRON_KEY_HERE';//TODO 修改你的cron job 密码
//  public $defaultAction = 'banner';

  public function actionIndex() {
    $info_hash = Yii::app()->request->getQuery('info_hash');
    if (strlen($info_hash) != 40 || hex2bin($info_hash) === FALSE) {
      echo 'Access Deny';
      Yii::app()->end();
    }
    $torrent = Yii::app()->db->createCommand('SELECT * FROM `hack_torrent` WHERE `info_hash`=:info_hash')
      ->queryRow(TRUE, array('info_hash' => hex2bin($info_hash)));
    if ($torrent) {
      Yii::setPathOfAlias('Imagine', Yii::getPathOfAlias('application.vendors.Imagine'));
      $imagine = new \Imagine\Gd\Imagine();
      $img = $imagine->open(Yii::app()->basePath . '/data/share.jpg');
      $img->draw()
        ->text(Yii::app()->format->size($torrent['downloaded']), new \Imagine\Gd\Font(Yii::app()->basePath . '/data/UbuntuMono-B.ttf', 12, $img->palette()
          ->color('000')), new \Imagine\Image\Point(212, 13));
      $img->draw()
        ->text(Yii::app()->format->number($torrent['seeder']), new \Imagine\Gd\Font(Yii::app()->basePath . '/data/UbuntuMono-B.ttf', 14, $img->palette()
          ->color('000')), new \Imagine\Image\Point(345, 13));
      $img->draw()
        ->text(Yii::app()->format->number($torrent['leacher']), new \Imagine\Gd\Font(Yii::app()->basePath . '/data/UbuntuMono-B.ttf', 14, $img->palette()
          ->color('000')), new \Imagine\Image\Point(465, 13));
      $img->draw()
        ->text($torrent['name'], new \Imagine\Gd\Font(Yii::app()->basePath . '/data/UbuntuMono-B.ttf', 9, $img->palette()
          ->color('000')), new \Imagine\Image\Point(3, 38));
      $img->show('jpg');
      Yii::app()->end();
    }
    echo 'Access Deny';
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
  }

}