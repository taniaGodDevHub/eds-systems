<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\ContactForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

$this->title = 'Обратная связь';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-12 col-md-4 ">
                <div class="card">
                    <div class="card-body">

                        <h1><?= Html::encode($this->title) ?></h1>

                        <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

                            <div class="alert alert-success">
                                Мы получили ваше сообщение. Ответим в ближайшее время
                            </div>

                        <?php else: ?>

                            <div class="row">
                                <div class="col-12">

                                    <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                                    <?= $form->field($model, 'name')->textInput(['autofocus' => true])->label('Имя') ?>

                                    <?= $form->field($model, 'email')->label('Почта для ответа') ?>

                                    <?= $form->field($model, 'subject')->label('Тема') ?>

                                    <?= $form->field($model, 'body')->textarea(['rows' => 6])->label('Сообщение') ?>


                                    <div class="form-group">
                                        <?= Html::submitButton('Отправить', ['class' => 'btn btn-success', 'name' => 'contact-button']) ?>
                                    </div>

                                    <?php ActiveForm::end(); ?>

                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
