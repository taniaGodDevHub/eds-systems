<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AuthItem $model */

$this->title = 'Изменить: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Роли и разрешения', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'name' => $model->name]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="auth-item-update">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h1><?= Html::encode($this->title) ?></h1>
                    </div>
                    <div class="card-subtitle">
                        Будьте внимательны! Эти изменения могут конуться большого числа пользователей.
                    </div>
                    <div class="sub-text mt-3">
                        <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
