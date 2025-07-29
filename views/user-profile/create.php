<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserProfile $model */

$this->title = 'Создать профиль';
$this->params['breadcrumbs'][] = ['label' => 'Профили пользователей', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
