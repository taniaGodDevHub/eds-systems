<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AuthItem $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList([1 => 'Роль', 2=>'Разрешение'], ['prompt' => 'Выбрать тип сущности']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->hiddenInput(['value' => empty($model->created_at) ? time() : $model->created_at])->label(false) ?>

    <?= $form->field($model, 'updated_at')->hiddenInput(['value' => time()])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
