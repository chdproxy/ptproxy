<?php

class UserController extends Controller {

  public function filters() {
    return array();
  }

  public function actionIndex() {
    echo Yii::app()->user->id;
  }

  /**
   * 后台登陆
   */
  public function actionLogin() {
    $model = new UserForm('login');
    if (isset($_POST['UserForm'])) {
      $model->attributes = $_POST['UserForm'];
      if ($model->validate()) {
        $this->redirect(Yii::app()->user->returnUrl);
      }
    }
    $this->render('login', array('model' => $model)); //GET方式请求的或者用户验证失败，则渲染登录表单
  }

  /**
   * 注销登录
   */
  public function actionLogout() {
    Yii::app()->user->logout();
    $this->redirect(Yii::app()->homeUrl);
  }

  public function actionEdit() {

//    $model = new User('update');
//    $form = new CForm('application.views.user.editForm', $model->findByPk(Yii::app()->user->id));
//    if ($form->submitted('edit') && $form->validate()) {
//      $this->redirect(array('edit', 'st' => 'suc'));
//    }
//    else {
//      $this->render('edit', array('form' => $form));
//    }
  }

  public function actionReset() {
    if (Yii::app()->request->isPostRequest) {
      $email = Yii::app()->request->getPost('email');
      $user = User::model()->findByAttributes(array('email' => $email));
      if ($user) {
        $new_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $user->password = CPasswordHelper::hashPassword($new_password);
        $user->save();
        $message = new YiiMailMessage();
        $message->view = 'password';
        $message->setCharset('utf-8');
        $message->setSubject('[ChinaHDTV.ORG]Password Reset');
        $message->setBody(array('password' => $new_password), 'text/plain');
        $message->addTo($user->email);
        $message->from = Yii::app()->mail->transportOptions['username'];
        Yii::app()->mail->send($message);
        Yii::app()->user->setFlash('account_reset', '邮件已经发送到 ' . $email);
      }
      else {
        Yii::app()->user->setFlash('account_reset', '不存在此条记录 ' . $email);
      }
    }
    $this->render('reset');
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