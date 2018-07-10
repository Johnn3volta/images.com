<?php

namespace frontend\models;

use Yii;
use yii\redis\Connection;

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
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @param \frontend\models\User $user
     */
    public function like(User $user){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $redis->sadd("post:{$this->getId()}:likes", $user->getId());
        $redis->sadd("user:{$user->getId()}:likes", $this->getId());
    }

    /**
     * @param \frontend\models\User $user
     */
    public function unLike(User $user){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $redis->srem("post:{$this->getId()}:likes", $user->getId());
        $redis->srem("user:{$user->getId()}:likes", $this->getId());
    }

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function countLikes(){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;

        return $redis->scard("post:{$this->getId()}:likes");
    }


    /**
     * @param \frontend\models\User $user
     *
     * @return mixed
     */
    public function isLikedBy(User $user){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;

        return $redis->sismember("post:{$this->getId()}:likes", $user->getId());
    }
}
