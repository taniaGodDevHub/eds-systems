<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-4 ">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h1><?= Html::encode($this->title) ?></h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            Заполните поля для регистрации:
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">

                            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
                            <?= $form->field($model, 'email') ?>
                            <?= $form->field($model, 'password')->passwordInput() ?>
                            <?= $form->field($model, 'tg_login')->textInput() ?>
                            <?= $form->field($model, 'role')->hiddenInput(['value' => 'user'])->label(false) ?>

                            <div class="form-group mt-5">
                                <?= Html::submitButton('Отправить', ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
                            </div>
                            <?php ActiveForm::end(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
