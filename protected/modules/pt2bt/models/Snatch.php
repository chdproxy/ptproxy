<?php

/**
 * This is the model class for table "snatch".
 *
 * The followings are the available columns in table 'snatch':
 * @property string $peer_id
 * @property string $info_hash
 * @property string $uploaded
 * @property string $downloaded
 * @property string $left
 * @property string $created
 * @property string $last_access
 */
class Snatch extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'snatch';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('peer_id, info_hash', 'required'),
			array('peer_id, info_hash, uploaded, downloaded, left', 'length', 'max'=>20),
			array('created, last_access', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('peer_id, info_hash, uploaded, downloaded, left, created, last_access', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'peer_id' => 'Peer',
			'info_hash' => 'Info Hash',
			'uploaded' => 'Uploaded',
			'downloaded' => 'Downloaded',
			'left' => 'Left',
			'created' => 'Created',
			'last_access' => 'Last Access',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('peer_id',$this->peer_id,true);
		$criteria->compare('info_hash',$this->info_hash,true);
		$criteria->compare('uploaded',$this->uploaded,true);
		$criteria->compare('downloaded',$this->downloaded,true);
		$criteria->compare('left',$this->left,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('last_access',$this->last_access,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Snatch the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
