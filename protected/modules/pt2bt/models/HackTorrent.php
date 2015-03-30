<?php

/**
 * This is the model class for table "hack_torrent".
 *
 * The followings are the available columns in table 'hack_torrent':
 * @property string $info_hash
 * @property integer $site
 * @property string $last_active
 * @property string $name
 * @property string $author
 * @property string $created
 * @property string $completed
 * @property string $uploaded
 * @property string $downloaded
 * @property integer $rss
 * @property integer $status
 * @property integer $seeder
 * @property integer $leacher
 * @property string $passkey
 */
class HackTorrent extends CActiveRecord {
  /**
   * @return string the associated database table name
   */
  public function tableName() {
    return 'hack_torrent';
  }

  /**
   * @return array validation rules for model attributes.
   */
  public function rules() {
    // NOTE: you should only define rules for those attributes that
    // will receive user inputs.
    return array(
      array('info_hash', 'required'),
      array('site, rss, status, seeder, leacher', 'numerical', 'integerOnly' => TRUE),
      array('info_hash', 'length', 'max' => 20),
      array('last_active, created, completed', 'length', 'max' => 11),
      array('name', 'length', 'max' => 255),
      array('author', 'length', 'max' => 60),
      array('uploaded, downloaded', 'length', 'max' => 22),
      array('passkey', 'length', 'max' => 16),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array(
        'info_hash, site, last_active, name, author, created, completed, uploaded, downloaded, rss, status, seeder, leacher, passkey',
        'safe',
        'on' => 'search'
      ),
    );
  }

  /**
   * @return array relational rules.
   */
  public function relations() {
    // NOTE: you may need to adjust the relation name and the related
    // class name for the relations automatically generated below.
    return array();
  }

  /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels() {
    return array(
      'info_hash' => 'Info Hash',
      'site' => 'Site',
      'last_active' => 'Last Active',
      'name' => 'Name',
      'author' => 'Author',
      'created' => 'Created',
      'completed' => 'Completed',
      'uploaded' => 'Uploaded',
      'downloaded' => 'Downloaded',
      'rss' => 'Rss',
      'status' => 'Status',
      'seeder' => 'Seeder',
      'leacher' => 'Leacher',
      'passkey' => 'Passkey',
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

    $criteria->compare('info_hash', $this->info_hash, TRUE);
    $criteria->compare('site', $this->site);
    $criteria->compare('last_active', $this->last_active, TRUE);
    $criteria->compare('name', $this->name, TRUE);
    $criteria->compare('author', $this->author, TRUE);
    $criteria->compare('created', $this->created, TRUE);
    $criteria->compare('completed', $this->completed, TRUE);
    $criteria->compare('uploaded', $this->uploaded, TRUE);
    $criteria->compare('downloaded', $this->downloaded, TRUE);
    $criteria->compare('rss', $this->rss);
    $criteria->compare('status', $this->status);
    $criteria->compare('seeder', $this->seeder);
    $criteria->compare('leacher', $this->leacher);
    $criteria->compare('passkey', $this->passkey, TRUE);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return HackTorrent the static model class
   */
  public static function model($className = __CLASS__) {
    return parent::model($className);
  }

  public function getReadAbleSiteName() {
    if (!isset($this->site)) {
      return '';
    }
    switch ($this->site) {
      case 0:
        return 'CHDBits';
        break;
      case 1:
        return 'HDWing';
        break;
      case 2:
        return 'TTG';
        break;
      default:
        return 'Unknown';
        break;
    }
  }
}
