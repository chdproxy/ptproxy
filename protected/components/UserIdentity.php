<?php
/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

  protected $_id;

  /**
   * Authenticates a user.
   * @return boolean whether authentication succeeds.
   */
  public function authenticate() {
    $user = User::model()->findByAttributes(array('username' => $this->username, 'status' => 1));
    if ($user && CPasswordHelper::verifyPassword($this->password, $user->password)) {
      $this->_id = $user->id;
      $user->login = time();
      $user->save();
      unset($user);
      $this->errorCode = self::ERROR_NONE;
    }
    else {
      $this->errorCode = self::ERROR_PASSWORD_INVALID;
      $this->errorMessage = 'ERROR_PASSWORD_INVALID';
    }
    return !$this->errorCode;
  }

  public function getId() {
    return $this->_id;
  }
}