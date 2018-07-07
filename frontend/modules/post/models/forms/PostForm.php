<?php


namespace frontend\modules\post\models\forms;


use frontend\models\Post;
use frontend\models\User;
use Yii;
use yii\base\Model;

class PostForm extends Model{

    const MAX_DESCRIPTION_LENGTH = 1000;

    public $picture;

    public $description;

    private $user;

    public function rules(){
        return [
            [
                ['picture'],
                'file',
                'skipOnEmpty'              => false,
                'extensions'               => ['jpg','png'],
                'checkExtensionByMimeType' => true,
                'maxSize'                  => $this->getMaxSize(),
            ],
            [['description'], 'string', 'max' => self::MAX_DESCRIPTION_LENGTH],
        ];
    }

    public function __construct(User $user){
        $this->user = $user;
        return parent::__construct();

    }

    public function save(){
        if($this->validate()){
            $post = new Post();
            $post->description = $this->description;
            $post->created_at = time();
            $post->filename = Yii::$app->storage->saveUploadedFile($this->picture);
            $post->user_id = $this->user->getId();
            return $post->save(false);

//            echo '<pre>';
//            print_r($this);
//            echo '</pre>';
//            die();
        }
    }

    private function getMaxSize(){
        return Yii::$app->params['maxFileSize'];
    }
}