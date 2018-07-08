<?php


namespace frontend\modules\post\models\forms;


use frontend\models\events\PostCreatedEvents;
use frontend\models\Post;
use frontend\models\User;
use Intervention\Image\ImageManager;
use Yii;
use yii\base\Model;

/**
 * Class PostForm
 *
 * @package frontend\modules\post\models\forms
 */
class PostForm extends Model{

    const MAX_DESCRIPTION_LENGTH = 1000;

    const EVENT_POST_CREATED = 'post_created';

    /**
     * @var
     */
    public $picture;

    /**
     * @var
     */
    public $description;

    /**
     * @var \frontend\models\User
     */
    private $user;

    /**
     * @return array
     */
    public function rules(){
        return [
            [
                ['picture'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => ['jpg', 'png'],
                'checkExtensionByMimeType' => true,
                'maxSize' => $this->getMaxSize(),
            ],
            [['description'], 'string', 'max' => self::MAX_DESCRIPTION_LENGTH],
        ];
    }

    /**
     * PostForm constructor.
     *
     * @param \frontend\models\User $user
     */
    public function __construct(User $user){
        $this->user = $user;
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'resizePicture']);
        $this->on(self::EVENT_POST_CREATED, [
            Yii::$app->FeedService,
            'addToFeeds',
        ]);

        return parent::__construct();

    }

    /**
     * @return bool
     */
    public function save(){
        if($this->validate()){
            $post = new Post();
            $post->description = $this->description;
            $post->created_at = time();
            $post->filename = Yii::$app->storage->saveUploadedFile($this->picture);
            $post->user_id = $this->user->getId();

            if($post->save(false)){
                $event = new PostCreatedEvents();
                $event->user = $this->user;
                $event->post = $post;
                $this->trigger(self::EVENT_POST_CREATED,$event);

                return true;
            }
        }

        return false;
    }

    /**
     * Resize image if needed
     */
    public function resizePicture(){
        if($this->picture->error){
            return;
        }

        $width = Yii::$app->params['profilePicture']['maxWidth'];
        $heigth = Yii::$app->params['profilePicture']['maxHeight'];

        $manager = new ImageManager(['driver' => 'imagick']);

        $image = $manager->make($this->picture->tempName);

        $image->resize($width, $heigth, function ($constraint){
//            для растянутых лиц
            $constraint->aspectRatio();

            $constraint->upsize();
        })->save();
    }

    /**
     * @return mixed
     */
    private function getMaxSize(){
        return Yii::$app->params['maxFileSize'];
    }
}