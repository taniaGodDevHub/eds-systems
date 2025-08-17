<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Client $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="client-form">
    <?php Pjax::begin(['id' => 'pjax-container-' . $model->id]);?>
    <?php $form = ActiveForm::begin(['action' => Url::to(['/client/update', 'id' => $model->id]), 'method' => 'post', 'options' => ['data-pjax' => true]]); ?>

    <?= $form->field($model, 'chat_id')->textInput(['disabled' => true]) ?>

    <?= $form->field($model, 'f')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'i')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'o')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
<?php Pjax::end();?>
</div>
