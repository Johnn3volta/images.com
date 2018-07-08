<?php

namespace frontend\models\events;


use frontend\models\Post;
use frontend\models\User;
use yii\base\Event;

/**
 * Class PostCreatedEvents
 *
 * @package frontend\models\events
 */
class PostCreatedEvents extends Event{

    /**
     * @var User
     */
    public $user;

    /**
     * @var Post
     */
    public $post;

    /**
     * @return User
     */
    public function getUser() : User{
        return $this->user;
    }

    /**
     * @return Post
     */
    public function getPost() : Post{
        return $this->post;
    }
}