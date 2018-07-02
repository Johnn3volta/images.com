<?php


namespace frontend\modules\user\controllers;


use frontend\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProfileController extends Controller{

    /**
     * @param $id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id){
        return $this->render('view',[
            'user' => $this->findUser($id)
        ]);
    }

    /**
     * @param $id
     *
     * @return array|\frontend\models\User|null|\yii\db\ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    private function findUser($id){
        if($user = User::find()->where(['id' => $id])->one()) return $user;
        throw new NotFoundHttpException();
    }
    
}