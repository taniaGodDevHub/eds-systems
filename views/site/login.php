<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-4 ">
            <div class="card">
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Логин') ?>

                    <?= $form->field($model, 'password')->passwordInput()->label('Пароль') ?>

                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    ])->label('Запомнить меня') ?>

                    <div class="form-group">
                        <div>
                            <?= Html::submitButton('Вход', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                    <div>
                        <?= Html::a('Восстановить доступ', ['site/request-password-reset']) ?>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
