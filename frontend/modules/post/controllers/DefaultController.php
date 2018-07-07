<?php

namespace frontend\modules\post\controllers;

use frontend\models\Post;
use frontend\modules\post\models\forms\PostForm;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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

    public function actionView($id){
        return $this->render('view',['post' => $this->findPost($id)]);
    }

    private function findPost($id){
        if($post = Post::findOne($id)) return $post;
        throw new NotFoundHttpException();
    }
}
