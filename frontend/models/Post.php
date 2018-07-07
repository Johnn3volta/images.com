<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string $description
 * @property int $created_at
 */
class Post extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id'          => 'ID',
            'user_id'     => 'User ID',
            'filename'    => 'Filename',
            'description' => 'Description',
            'created_at'  => 'Created At',
        ];
    }

    /**
     * @return mixed
     */
    public function getImage(){
        return Yii::$app->storage->getFile($this->filename);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(){
        return $this->hasOne(User::class,['id' => 'user_id']);
    }
}
