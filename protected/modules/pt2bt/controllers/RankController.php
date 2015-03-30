<?php

class RankController extends Controller {

  public function accessRules() {
    return array(
      array(
        'allow',
        'actions' => array('index',),
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
    $current_tops = Yii::app()->db->createCommand('select count(`sn`.`peer_id`) as nums,HEX(`sn`.`info_hash`) as info_hash, `ht`.`name` as title from `snatch` as sn INNER JOIN  `hack_torrent` as ht WHERE `sn`.`info_hash`=`ht`.`info_hash` group by `info_hash` order by nums desc LIMIT 30')
      ->queryAll();
    $top30_dataProvider = new CArrayDataProvider($current_tops, array(
      'keyField' => 'info_hash',
    ));
    $top100 = Yii::app()->db->createCommand('select HEX(info_hash) as info_hash,`name` as title,completed from hack_torrent where created>:oneweek order by completed desc limit 100')
      ->queryAll(true,array('oneweek' => strtotime('first day of this month midnight')));
    $top100_dataProvider = new CArrayDataProvider($top100, array(
      'keyField' => 'info_hash',
    ));
    $this->render('index', array(
      'top30_dataProvider' => $top30_dataProvider,
      'top100_dataProvider' => $top100_dataProvider,
    ));

  }

}