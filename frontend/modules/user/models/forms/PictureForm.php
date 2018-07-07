<?php

namespace frontend\modules\user\models\forms;

use Intervention\Image\ImageManager;
use Yii;
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
     * PictureForm constructor.
     */
    public function __construct(){
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'resizePicture']);

        return parent::__construct();
    }

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
                'maxSize' => $this->getMaxFileSize()
            ],
        ];
    }

    /**
     * @return int
     */
    public function save(){
        return 1;
    }

    /**
     * Resize image if needed
     */
    public function resizePicture(){
        if($this->picture->error) return;

        $width = Yii::$app->params['profilePicture']['maxWidth'];
        $heigth = Yii::$app->params['profilePicture']['maxHeight'];

        $manager = new ImageManager(['driver' => 'imagick']);

        $image = $manager->make($this->picture->tempName);

        $image->resize($width, $heigth, function ($constraint){
//            для растянутых лиц
//            $constraint->aspectRatio();

            $constraint->upsize();
        })->save();
    }

    /**
     * @return mixed
     */
    private function getMaxFileSize(){
        return Yii::$app->params['maxFileSize'];
    }
}