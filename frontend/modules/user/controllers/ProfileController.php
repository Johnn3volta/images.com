<?php


namespace frontend\modules\user\controllers;


use frontend\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class ProfileController
 *
 * @package frontend\modules\user\controllers
 */
class ProfileController extends Controller{

    /**
     * @param $nickname
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($nickname){
        return $this->render('view', [
            'user' => $this->findUser($nickname),
            'currentUser' => Yii::$app->user->identity
        ]);
    }

    /**
     * @param $nickname
     *
     * @return User
     * @throws \yii\web\NotFoundHttpException
     */
    private function findUser($nickname){
        if($user = User::find()
                       ->where(['nickname' => $nickname])
                       ->orWhere(['id' => $nickname])
                       ->one()){
            return $user;
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionSubscribe($id){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/user/default/login']);
        }

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;

        $user = $this->findUserById($id);

        $currentUser->followUser($user);

        return $this->redirect([
            '/user/profile/view',
            'nickname' => $user->getNickName(),
        ]);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUnsubscribe($id){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/user/default/login']);
        }

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        $user = $this->findUser($id);

        $currentUser->unfollowUser($user);

        return $this->redirect([
            '/user/profile/view',
            'nickname' => $user->getNickname(),
        ]);
    }

    /**
     * @param $id
     *
     * @return \frontend\models\User|null
     * @throws \yii\web\NotFoundHttpException
     */
    private function findUserById($id){
        if($user = User::findOne($id)){
            return $user;
        }
        throw new NotFoundHttpException();
    }

//    public function actionGenerate(){
//        $faker = \Faker\Factory::create();
//        for ($i = 0;$i < 1000; $i++){
//            $user = new User([
//                'username' => $faker->name,
//                'email' => $faker->email,
//                'about' => $faker->text(200),
//                'nickname' => $faker->regexify('[A-Za-z0-9_]{5,15}'),
//                'auth_key' => \Yii::$app->security->generateRandomString(),
//                'password_hash' => \Yii::$app->security->generateRandomString(),
//                'created_at' => $time = time(),
//                'updated_at' => $time,
//            ]);
//            $user->save(false);
//        }
//    }

}