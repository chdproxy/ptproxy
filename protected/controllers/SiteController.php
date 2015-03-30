<?php

class SiteController extends Controller {
  public function actionError() {
    @header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
    @header('Pragma: no-cache'); // HTTP 1.0.
    @header('Expires: 0'); // Proxies.
    @header('Content-type: application/json');
    if (Yii::app()->getErrorHandler()->error['code'] == 404) {
      echo CJSON::encode(array(
          'code' => 404,
          'msg' => 'page not found',
          'data' => array('ip' => Yii::app()->getRequest()->getUserHostAddress())
        ));
      Yii::app()->end();
    }
    echo CJSON::encode(array(
      'code' => Yii::app()->errorHandler->error['code'],
      'msg' => Yii::app()->errorHandler->error['message'],
      'data' => NULL,
    ));
    Yii::app()->end();
  }

  public function actionIndex() {
//    $this->redirect('feed/');
    $this->render('index');
  }

  public function actionInstall() {
//    $this->redirect('feed/');
    echo Yii::app()->user->isGuest;

  }

  // Uncomment the following methods and override them if needed
  /*
  public function filters()
  {
      // return the filter configuration for this controller, e.g.:
      return array(
          'inlineFilterName',
          array(
              'class'=>'path.to.FilterClass',
              'propertyName'=>'propertyValue',
          ),
      );
  }

  public function actions()
  {
      // return external action classes, e.g.:
      return array(
          'action1'=>'path.to.ActionClass',
          'action2'=>array(
              'class'=>'path.to.AnotherActionClass',
              'propertyName'=>'propertyValue',
          ),
      );
  }
  */
}