<?php
namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\redis\Connection;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property string $about
 * @property string $picture
 * @property string $nickname
 * @property integer $type
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface{

    const STATUS_DELETED = 0;

    const STATUS_ACTIVE = 10;

    const NO_IMAGE = '/img/no-image.png';

    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(){
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [
                'status',
                'in',
                'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id){
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public static function findIdentityByAccessToken($token, $type = null){
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $email
     *
     * @return static|null
     */
    public static function findByEmail($email){
        return static::findOne([
            'email'  => $email,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token){
        if(!static::isPasswordResetTokenValid($token)){
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token){
        if(empty($token)){
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(){
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(){
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     *
     * @return bool
     */
    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password){
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws \yii\base\Exception
     */
    public function setPassword($password){
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @throws \yii\base\Exception
     */
    public function generateAuthKey(){
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken(){
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken(){
        $this->password_reset_token = null;
    }

    /**
     * @return int|mixed|string
     */
    public function getNickName(){
        return ($this->nickname) ? $this->nickname : $this->getId();
    }

    /**
     * Subscribe current user to given user
     *
     * @param \frontend\models\User $user
     */
    public function followUser(User $user){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $redis->sadd("user:{$this->getId()}:subscriptions", $user->getId());
        $redis->sadd("user:{$user->getId()}:followers", $this->getId());
    }

    /**
     * Unsubscribe current user from given user
     *
     * @param \frontend\models\User $user
     */
    public function unfollowUser(User $user){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $redis->srem("user:{$this->getId()}:subscriptions", $user->getId());
        $redis->srem("user:{$user->getId()}:followers", $this->getId());
    }

    /**
     * @return array|\frontend\models\User[]|\yii\db\ActiveRecord[]
     */
    public function getSubscriptions(){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $key = "user:{$this->getId()}:subscriptions";
        $ids = $redis->smembers($key);

        return User::find()
                   ->select('id, username, nickname')
                   ->where(['id' => $ids])
                   ->orderBy('username')
                   ->asArray()
                   ->all();
    }

    /**
     * @return array|\frontend\models\User[]|\yii\db\ActiveRecord[]
     */
    public function getFollowers(){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;
        $key = "user:{$this->getId()}:followers";
        $ids = $redis->smembers($key);

        return User::find()
                   ->select('id, username, nickname')
                   ->where(['id' => $ids])
                   ->orderBy('username')
                   ->asArray()
                   ->all();
    }

    /**
     * @return mixed
     */
    public function countFollowers(){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;

        return $redis->scard("user:{$this->getId()}:followers");
    }

    /**
     * @return mixed
     */
    public function countSubscriptions(){
        /* @var $redis Connection */
        $redis = Yii::$app->redis;

        return $redis->scard("user:{$this->getId()}:subscriptions");
    }

    /**
     * @param \frontend\models\User $user
     *
     * @return array|\frontend\models\User[]|\yii\db\ActiveRecord[]
     */
    public function getMutualSubscriptionsTo(User $user){
        $key1 = "user:{$this->getId()}:subscriptions";
        $key2 = "user:{$user->getId()}:followers";
        /* @var $redis Connection*/
        $redis = Yii::$app->redis;

        $ids = $redis->sinter($key1,$key2);

        return User::find()->select('id,username,nickname')->where(['id' => $ids])->orderBy('username')->asArray()->all();
    }

    /**
     * @return mixed
     */
    public function getPicture(){
        if($this->picture){
            return Yii::$app->storage->getFile($this->picture);
        }

        return self::NO_IMAGE;
    }

    /**
     * @return bool
     */
    public function deletePicture(){
        if($this->picture && Yii::$app->storage->deleteFile($this->picture)){
            $this->picture = null;
            return $this->save(false,['picture']);
        }
        return false;
    }

    /**
     * @param int $limit
     *
     * @return array|\frontend\models\User[]|\yii\db\ActiveRecord[]
     */
    public function getFeed(int $limit){
        $order = ['post_created_at' => SORT_DESC];
        return $this->hasMany(Feed::class,['user_id'=> 'id'])->orderBy($order)->limit($limit)->all();
    }

    /**
     * @param int $post_id
     *
     * @return mixed
     */
    public function likesPost(int $post_id){
        /* @var $redis Connection*/
        $redis = Yii::$app->redis;

        return $redis->sismember("user:{$this->id}:likes",$post_id);
    }
}
