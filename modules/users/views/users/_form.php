<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput()?>
    <?= $form->field($model, 'email')->textInput()?>
    <?= $form->field($model, 'password')->textInput(['value' => ''])?>
    <?= $form->field($model, 'status')->dropDownList([0 => 'Заблокирован', 10 => 'Активен']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
