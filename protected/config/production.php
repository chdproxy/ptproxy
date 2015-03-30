<?php
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
  'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
  'name' => 'ChinaHDTV.ORG',
  'defaultController' => 'site/index',
  'preload' => array('log'),
  'language' => 'zh_cn',
  'sourceLanguage' => 'zh_cn',
  'charset' => 'utf-8',
  'timeZone' => 'Asia/Shanghai',
  'import' => array(
    'application.models.*',
    'application.components.*',
    //yii-mail
    'ext.yii-mail.YiiMailMessage'
  ),
  'modules' => array(
    'pt2bt' => array(
      'hack_tracker_host' => '替换成你运行该程序的服务器IP地址', //TODO 重要 替换成你运行该程序的服务器IP地址
      'hack_tracker_port' => 8080, //TODO 重要 替换成你运行该程序的服务器端口 （http）
      'hack_tracker_auth_ips' => array('*'),
      'rss_auth_ips' => array('*'),
      'torrent_save_path' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../torrent/'
    )
  ),
  'components' => array( // application components
    'mail' => array(
      'class' => 'ext.yii-mail.YiiMail',
      'transportType' => 'smtp',
      'transportOptions' => array(
        'host' => 'YOUR_SMTP_HOST', //TODO 替换成你SMTP服务器信息
        'username' => 'YOUR_EMAIL_ACCOUNT',
        'password' => 'YOUR_EMAIL_PASSWORD',
        'port' => '25',
      ),
      'viewPath' => 'application.views.yii-mail',
      'logging' => TRUE,
      'dryRun' => FALSE
    ),
    'cache' => array(
      'class' => 'CMemCache',
      'servers' => array(
        array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 90),
      ),
    ),
//    'assetManager'=>array(),
    'urlManager' => array(
      'urlFormat' => 'path',
      'rules' => array(
        //webapi
//        array('webapi/<action>', 'pattern' => 'api/<action>', 'urlSuffix' => '.json', 'verb' => 'GET'),
        //array('webapi/<action>Create', 'pattern'=>'api/<action>', 'urlSuffix'=>'.json','verb'=>'POST'),
        //array('webapi/<action>Update', 'pattern'=>'api/<action>', 'urlSuffix'=>'.json','verb'=>'PUT'),
        //array('webapi/<action>Delete', 'pattern'=>'api/<action>', 'urlSuffix'=>'.json','verb'=>'DELETE'),
        'announce' => 'pt2bt/tracker/announce',
        'scrape' => 'pt2bt/tracker/scrape',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
      ),
      'showScriptName' => FALSE,
      'caseSensitive' => FALSE,
    ),
    /*    'db' => array(
          'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../data/chinahdtv.db',
        ),*/
    'db' => array(
      'connectionString' => 'mysql:host=localhost;port=3316;dbname=chinahdtv',
      'emulatePrepare' => TRUE,
      'username' => 'root',
      'password' => 'root',
      'charset' => 'utf8',
	  'schemaCachingDuration'=>86400,
    ),
    'request' => array(
      'enableCsrfValidation' => FALSE,
      'csrfTokenName' => 'CSRF_TOKEN'
    ),
    'session' => array(
      'sessionName' => 'SESSION',
      'autoStart' => TRUE,
      'cacheID' => 'cache',
      'cookieMode' => 'only',
      'timeout' => 3600,
      'class' => 'CCacheHttpSession',
    ),
    'user' => array(
      'class' => 'WebUser',
      'allowAutoLogin' => TRUE,
      'loginUrl' => array('/user/login'),
    ),
    'authManager' => array(
      'class' => 'CDbAuthManager',
      'assignmentTable' => 'authassignment',
      'itemChildTable' => 'authitemchild',
      'itemTable' => 'authitem',
    ),
    'errorHandler' => array(
      'errorAction' => 'site/error',
    ),
    'log' => array(
      'class' => 'CLogRouter',
      'routes' => array(
        array(
          'class' => 'CFileLogRoute',
          'levels' => 'error, warning',
		  'logFile' => date('Y-m-d') . '_' . php_sapi_name() . '.log',
          'maxLogFiles' => 30
        ),
        // uncomment the following to show log messages on web pages
        /*
        array(
            'class'=>'CWebLogRoute',
        ),
        */
      ),
    ),
  ),
  // application-level parameters that can be accessed
  // using Yii::app()->params['paramName']
  'params' => array(
    'adminEmail' => 'admin@example.com',
  ),
);
