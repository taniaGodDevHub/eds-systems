<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AuthItemChild $model */

$this->title = 'И: ' . $model->parent;
$this->params['breadcrumbs'][] = ['label' => 'Auth Item Children', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->parent, 'url' => ['view', 'parent' => $model->parent, 'child' => $model->child]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-item-child-update">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h1><?= Html::encode($this->title) ?></h1>
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
