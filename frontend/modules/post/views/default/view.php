<?php
/**
 * @var $this yii\web\View;
 * @var $post frontend\models\Post;
 * @var $currentUser frontend\models\User;
 */
use yii\helpers\Html;
use yii\web\JqueryAsset;

?>

<div class="post-default-index">
    <div class="row">
        <div class="col-md-12">
            <?php if($post->getUser()): ?>
                <?= $post->user->username ?>
            <?php endif; ?>
        </div>
        <div class="col-md-12">
            <?= Html::img($post->getImage()) ?>
        </div>
        <div class="col-md-12">
            <?= Html::encode($post->description) ?>
        </div>

      <div class="col-md-12">
        Likes: <span class="likes-count"><?=$post->countLikes() ?></span>
        <a href="#" class="btn btn-primary button-like" <?= ($currentUser && $post->isLikedBy($currentUser)) ? "style='display:none'" : "" ?> data-id="<?= $post->id ?>">
          Like &nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span>
        </a>
        <a href="#" class="btn btn-primary button-unlike" <?= ($currentUser && $post->isLikedBy($currentUser)) ? "" : "style='display:none'" ?>  data-id="<?= $post->id ?>">
          Unlike &nbsp;&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
        </a>
      </div>
    </div>
</div>
<?php $this->registerJsFile('@web/js/likes.js',['depends'=> JqueryAsset::class]) ?>
