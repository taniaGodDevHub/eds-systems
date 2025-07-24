<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AuthItemChild $model */

$this->title = 'Создать наследование';
$this->params['breadcrumbs'][] = ['label' => 'Наследование ролей и разрешений', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-child-create">
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
