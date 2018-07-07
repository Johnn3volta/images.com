<?php

namespace frontend\modules\post\controllers;

use frontend\models\Post;
use frontend\models\User;
use frontend\modules\post\models\forms\PostForm;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Default controller for the `post` module
 */
class DefaultController extends Controller{

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate(){
        $model = new PostForm(Yii::$app->user->identity);

        if($model->load(Yii::$app->request->post())){
            $model->picture = UploadedFile::getInstance($model, 'picture');
            if($model->save()){
                Yii::$app->session->setFlash('success', 'Пост создан');

                return $this->goHome();
            }
        }

        return $this->render('create', compact('model'));
    }

    /**
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id){
        return $this->render('view', [
            'post' => $this->findPost($id),
            'currentUser' => Yii::$app->user->identity
        ]);
    }

    /**
     * @return array|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionLike(){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/user/default/login']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $post = $this->findPost($id);

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        $post->like($currentUser);

        return [
            'success'    => true,
            'likesCount' => $post->countLikes(),
        ];
    }

    /**
     * @return array|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUnlike(){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/user/default/login']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        $post = $this->findPost($id);

        $post->unLike($currentUser);

        return [
            'success'    => true,
            'likesCount' => $post->countLikes(),
        ];
    }

    /**
     * @param $id
     *
     * @return \frontend\models\Post|null
     * @throws \yii\web\NotFoundHttpException
     */
    private function findPost($id){
        if($post = Post::findOne($id)){
            return $post;
        }
        throw new NotFoundHttpException();
    }
}
