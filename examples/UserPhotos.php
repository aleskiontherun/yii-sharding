<?php

/**
 * This is the model class for table "UserPhotos".
 *
 * The followings are the available columns in table 'UserPhotos':
 * @property string $id
 * @property integer $user_id
 * @property string $created_at
 * @property string $type
 * @property integer $width
 * @property integer $height
 */
class UserPhotos extends UserShardedRecord
{
	public $image;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className
	 * @return UserPhotos the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('image', 'file', 'types' => 'jpg, jpeg, gif, png', 'on' => 'insert'),
		);
	}

	protected function beforeSave()
	{
		if (!parent::beforeSave())
			return false;
		$this->id = uniqid();
		return true;
	}

}
