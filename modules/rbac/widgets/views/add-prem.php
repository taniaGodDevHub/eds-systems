<?php
use yii\bootstrap5\Html;

?>
<?php if(Yii::$app->user->can('admin')):?>
    <div class="row">
        <div class="col-12 col-md-4">
            В форме можно разрешить доступ к запрошенной странице для группы пользователей.<br>
            Важно понимать, что это действие распространится по всей системе.
        </div>
        <div class="col-12 col-md-4">
            <?= Html::beginForm(['/rbac/default/add-prem'], 'post', ['enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'premissionName', ACTION_UID) ?>
                <div class="input-group mb-3">
                    <?= Html::dropDownList('role', false, $list, ['prompt' => 'Выбрать роль для привязки разрешения', 'class' => 'form-control', 'aria-describedby' => 'basic-addon2']) ?>
                    <?= Html::submitButton('<i class="bi bi-arrow-return-right"></i>', ['id'=>'basic-addon2', 'class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
<?php endif;?>
