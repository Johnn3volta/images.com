<?php
/**
 * @var $this yii\web\View;
 * @var $post frontend\models\Post;
 */
use yii\helpers\Html;

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
    </div>
</div>
