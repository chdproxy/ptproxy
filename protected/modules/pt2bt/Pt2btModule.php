<?php

class Pt2btModule extends CWebModule {

  public $hack_tracker_host = '127.0.0.1'; //作弊的代理tracker地址，即本程序运行的服务器地址
  public $hack_tracker_port = 80; //监听端口
  public $hack_tracker_auth_ips = array('*'); //白名单模式，允许的IP列表
  public $torrent_save_path = FALSE; //种子文件存放位置
  public $rss_auth_ips = array('*'); //RSS种子允许的IP列表
  public $auto_peer_expr = 432000; // 5 days

  public function init() {
    // this method is called when the module is being created
    // you may place code here to customize the module or the application

    // import the module-level models and components
    $this->setImport(array(
      'pt2bt.models.*',
      'pt2bt.components.*',
    ));
    if (!$this->torrent_save_path) {
      $this->torrent_save_path = Yii::app()->getRuntimePath();
    }
  }

  public function beforeControllerAction($controller, $action) {
    if (parent::beforeControllerAction($controller, $action)) {
      // this method is called before any module controller action is performed
      // you may place customized code here
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}
