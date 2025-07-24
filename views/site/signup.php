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

                            <div class="row">
                                <div class="col-12">
                                    <b>Выберите роль:</b>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">

                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="SignupForm[role]" id="flexRadioDefault2" checked value="user">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                            Заказчик
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="SignupForm[role]" id="flexRadioDefault1" value="manufacturer">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Производитель
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="SignupForm[role]" id="flexRadioDefault3" value="provider">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Поставщик
                                        </label>
                                    </div>
                                </div>
                            </div>

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
