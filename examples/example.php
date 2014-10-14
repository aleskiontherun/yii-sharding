<?php
/**
 * @author: Aleksei Vesnin <dizeee@dizeee.ru>
 */

$user_id = Yii::app()->user->id;

// Create record
$model = new UserPhotos();
$model->image = CUploadedFile::getInstanceByName(get_class($model));
// All magic is done by setting the sharding attribute which is user_id
$model->user_id = $user_id;
$model->save();


// Get all user photos
$models = UserPhotos::model()->user($user_id)->findAll();
// Same as:
$models = UserPhotos::model()->shardKey($user_id)->findAll();
