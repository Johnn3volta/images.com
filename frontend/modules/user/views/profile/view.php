<?php
/**
 * @var $this yii\web\view;
 * @var $user frontend\models\User;
 * @var $currentUser frontend\models\User;
 */

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

?>
<h1> <?= Html::encode($user->username) ?></h1>
<p><?= HtmlPurifier::process($user->about) ?></p>
<hr>
<?= Html::a('Подписаться', [
    '/user/profile/subscribe',
    'id' => $user->getId(),
], ['class' => 'btn btn-success', 'title' => 'Подписаться']) ?>
&nbsp;&nbsp;&nbsp;
<?= Html::a('Отписаться', [
    '/user/profile/unsubscribe',
    'id' => $user->getId(),
], ['class' => 'btn btn-danger', 'title' => 'Отписаться']) ?>
<hr>
<?php if($currentUser): ?>
  <h5>Friend's? who are olso following <?= $user->username ?>:</h5>
  <div class="row">
      <?php foreach ($currentUser->getMutualSubscriptionsTo($user) as $item): ?>
        <div class="col-md-12">
            <?= Html::a(Html::encode($item['username']), [
                '/user/profile/view',
                'nickname' => $item['nickname'] ? $item['nickname'] : $item['id'],
            ]) ?>
        </div>
      <?php endforeach; ?>
  </div>
<?php endif; ?>
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal"
        data-target="#myModal1">
  Подписок: <?= $user->countSubscriptions() ?>
</button>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal"
        data-target="#myModal2">
  Подписчиков: <?= $user->countFollowers() ?>
</button>

<!-- Modal -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Subscriptions</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <?php foreach ($user->getSubscriptions() as $subsription): ?>
              <div class="col-md-12">
                  <?= Html::a(Html::encode($subsription['username']), [
                      '/user/profile/view',
                      'nickname' => $subsription['nickname'] ? $subsription['nickname'] : $subsription['id'],
                  ]) ?>
              </div>
            <?php endforeach; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Subscriptions</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <?php foreach ($user->getFollowers() as $follower): ?>
              <div class="col-md-12">
                  <?= Html::a(Html::encode($follower['username']), [
                      '/user/profile/view',
                      'nickname' => $follower['nickname'] ? $follower['nickname'] : $follower['id'],
                  ]) ?>
              </div>
            <?php endforeach; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
