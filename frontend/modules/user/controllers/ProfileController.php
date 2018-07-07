<?php


namespace frontend\modules\user\controllers;


use frontend\models\User;
use frontend\modules\user\models\forms\PictureForm;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

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
        $modelPicture = new PictureForm();

        return $this->render('view', [
            'user'         => $this->findUser($nickname),
            'currentUser'  => Yii::$app->user->identity,
            'modelPicture' => $modelPicture,
        ]);
    }

    public function actionUploadPicture(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new PictureForm();
        $model->picture = UploadedFile::getInstance($model, 'picture');

        if($model->validate()){
            /* @var $user User */
            $user = Yii::$app->user->identity;
            $user->picture = Yii::$app->storage->saveUploadedFile($model->picture);

            if($user->save(false, ['picture'])){
                return [
                    'success'    => true,
                    'pictureUri' => $user->getPicture(),
                ];
            }
        }

        return ['success' => false, 'errors' => $model->getErrors()];
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

    public function actionDeletePicture(){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/user/default/login']);
        }
            /* @var $currentuser User*/
        $currentuser = Yii::$app->user->identity;

        if($currentuser->deletePicture()){
            Yii::$app->session->setFlash('success','Картинка удалена');
        }else{
            Yii::$app->session->setFlash('danger','Неизвестная ошибка');
        }

        return $this->redirect(['/user/profile/view','nickname' => $currentuser->getNickName()]);
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