<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\users\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h1><?= Html::encode($this->title) ?></h1>
                    </div>
                    <div class="card-subtitle">
                        <?= Html::a('Добавить ', ['create'], ['class' => 'btn btn-success']) ?>
                    </div>
                    <div class="sub-text mt-3">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                'id',
                                'username:ntext:Логин',
                                //'auth_key',
                                //'password_hash',
                                //'password_reset_token',
                                'email:email',
                                [

                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'header' => '<i class="bi bi-lock-fill"></i>',
                                    'content' => function ($model) {
                                        if($model->username == 'admin'){
                                            return '<i class="bi bi-unlock-fill text-success me-3" style="    font-size: 22px;"></i>';
                                        }
                                        $block = '<div class ="d-flex"><i class="bi bi-unlock-fill text-success me-3" style="    font-size: 22px;"></i> '
                                            . Html::beginForm(['/users/users/update-status'])
                                            . Html::hiddenInput('user_id', $model->id)
                                            . Html::hiddenInput('status', 0)
                                            . Html::submitButton(
                                                'Заблокировать',
                                                ['class' => 'btn btn-danger btn-sm']
                                            )

                                            . Html::endForm() . '</div>';
                                        $unblock = '<div class ="d-flex"><i class="bi bi-lock-fill text-danger me-3" style="    font-size: 22px;"></i> '
                                            . Html::beginForm(['/users/users/update-status'])
                                            . Html::hiddenInput('user_id', $model->id)
                                            . Html::hiddenInput('status', 10)
                                            . Html::submitButton(
                                                'Разблокировать',
                                                ['class' => 'btn btn-success btn-sm']
                                            )
                                            . Html::endForm() . '</div>';
                                        return $model->status == 10 ? $block : $unblock;
                                    }
                                ],
                                'created_at:datetime',
                                //'updated_at',
                            ],
                        ]); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
