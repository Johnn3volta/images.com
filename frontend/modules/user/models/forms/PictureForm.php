<?php

namespace frontend\modules\user\models\forms;

use yii\base\Model;

/**
 * Class PictureForm
 *
 * @package frontend\modules\user\models\forms
 */
class PictureForm extends Model{

    /**
     * @var
     */
    public $picture;

    /**
     * @return array
     */
    public function rules(){
        return [
            [
                ['picture'],
                'file',
                'extensions'               => ['jpg'],
                'checkExtensionByMimeType' => true,
            ],
        ];
    }

    /**
     * @return int
     */
    public function save(){
        return 1;
    }
}