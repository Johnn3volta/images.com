<?php


namespace frontend\components;


use frontend\models\Feed;
use yii\base\Component;
use yii\base\Event;

/**
 * Class FeedService
 *
 * @package frontend\components
 *
 * @author Admin
 */
class FeedService extends Component{

    /**
     * @param \yii\base\Event $event
     */
    public function addToFeeds(Event $event){
        /* @var $user \frontend\models\User */
        $user = $event->getUser();
        /* @var $post \frontend\models\Post */
        $post = $event->getPost();
        $followers = $user->getFollowers();
        foreach ($followers as $follower) {
            $feedItem = new Feed();
            $feedItem->user_id = $follower['id'];
            $feedItem->author_id = $user->id;
            $feedItem->author_name = $user->username;
            $feedItem->author_nickname = $user->getNickName();
            $feedItem->author_picture = $user->getPicture();
            $feedItem->author_picture = $user->getPicture();
            $feedItem->post_id = $post->id;
            $feedItem->post_filename = $post->filename;
            $feedItem->post_description = $post->description;
            $feedItem->post_created_at = $post->created_at;
            $feedItem->save();
        }

    }
}