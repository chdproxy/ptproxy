<?php
class UserForm extends BaseForm {
  public $username;
  public $password;
  private $_identity;

  public function rules() {
    return array(
      array('username,password', 'required', 'on' => 'login,register'),
      array('password', 'authenticate', 'on' => 'login'),
    );
  }

  public function authenticate($attribute, $params) {
    $this->_identity = new UserIdentity($this->username, $this->password);
    if ($this->_identity->authenticate()) {
      Yii::app()->user->login($this->_identity, 3600 * 24 * 3);
    }
    else {
      $this->addError('password', $this->_identity->errorMessage);
    }
  }
}