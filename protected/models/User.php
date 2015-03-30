<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property integer $created
 * @property integer $access
 * @property integer $login
 * @property integer $status
 *
 */
class User extends CActiveRecord {
  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return User the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'user';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('username, password, email', 'required'),
      array('created, access, login, status', 'numerical', 'integerOnly' => TRUE),
      array('username', 'length', 'max' => 60),
      array('password', 'length', 'max' => 128),
      array('email', 'length', 'max' => 254),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('id, username,email, created, access, login, status', 'safe', 'on' => 'search'),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array(
      'torrents' => array(self::HAS_MANY, 'Torrent', 'id'),
    );
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
      'id' => 'Id',
      'username' => 'Username',
      'password' => 'Password',
      'email' => 'Email',
      'created' => 'Created',
      'access' => 'Access',
      'login' => 'Login',
      'status' => 'Status',
    );
  }

  /**
   * Retrieves a list of models based on the current search/filter conditions.
   *
   * Typical usecase:
   * - Initialize the model fields with values from filter form.
   * - Execute this method to get CActiveDataProvider instance which will filter
   * models according to data in model fields.
   * - Pass data provider to CGridView, CListView or any similar widget.
   *
   * @return CActiveDataProvider the data provider that can return the models
   * based on the search/filter conditions.
   */
  public function search() {
    // @todo Please modify the following code to remove attributes that should not be searched.

    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id, TRUE);
    $criteria->compare('username', $this->username, TRUE);
    $criteria->compare('password', $this->password, TRUE);
    $criteria->compare('email', $this->email, TRUE);
    $criteria->compare('created', $this->created);
    $criteria->compare('access', $this->access);
    $criteria->compare('login', $this->login);
    $criteria->compare('status', $this->status);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}
