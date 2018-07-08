<?php

/* @var $this yii\web\View */
/* @var $currentUser \frontend\models\User */

/* @var $feedItems [] \frontend\models\Feed */


use \yii\helpers\Html;
use yii\web\JqueryAsset;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <?php if($feedItems): ?>
        <?php foreach ($feedItems as $feedItem): ?>
            <?php /* @var $feedItem \frontend\models\Feed */ ?>
        <div class="row">
          <div class="col-md-12">
              <?= Html::img($feedItem->author_picture, [
                  'width' => 30,
                  'height' => 30,
              ]) ?>
              <?= Html::a(Html::encode($feedItem->author_name), [
                  '/user/profile/view',
                  'nickname' => $feedItem->author_nickname ? $feedItem->author_nickname : $feedItem->id,
              ]) ?>
          </div>

            <?= Html::img(Yii::$app->storage->getFile($feedItem->post_filename)) ?>
          <div class="col-md-12">
              <?= \yii\helpers\HtmlPurifier::process($feedItem->post_description) ?>
          </div>
          <div class="col-md-12">
              <?= Yii::$app->formatter->asDatetime($feedItem->post_created_at) ?>
          </div>
        </div>
        <div class="col-md-12">
          Likes: <span class="likes-count"><?= $feedItem->countLikes() ?></span>
          <a href="#" class="btn btn-primary button-like" <?= ($currentUser && $currentUser->likesPost($feedItem->post_id)) ? "style='display:none'" : "" ?> data-id="<?= $feedItem->id ?>">
            Like &nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span>
          </a>
          <a href="#" class="btn btn-primary button-unlike" <?= ($currentUser &&  $currentUser->likesPost($feedItem->post_id)) ? "" : "style='display:none'" ?>  data-id="<?= $feedItem->id ?>">
            Unlike &nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
          </a>
        </div>
        <hr>
        <?php endforeach; ?>
    <?php else: ?>
      <div class="col-md-12">
        НЕт новотей
      </div>
    <?php endif; ?>
</div>
<?php $this->registerJsFile('@web/js/likes.js',['depends'=> JqueryAsset::class]) ?>