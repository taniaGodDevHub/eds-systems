<?php

use app\models\AuthItem;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\AuthItemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Роли и разрешения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h1><?= Html::encode($this->title) ?></h1>
                    </div>
                    <div class="card-subtitle">
                        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
                    </div>
                    <div class="sub-text mt-3">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                'name',
                                [
                                    'attribute' => 'type',
                                    'content' => function($model){

                                        return $model->type == 1 ? 'Роль': 'Разрешение';

                                    },
                                    'filter' => [1 => 'Роль', 2 => 'Разрешение']
                                ],

                                'description:ntext',
                                //'rule_name',
                                //'data',
                                //'created_at',
                                'updated_at:datetime',
                                [
                                    'class' => ActionColumn::className(),
                                    'urlCreator' => function ($action, AuthItem $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'name' => $model->name]);
                                    },
                                    'template' => '{update} {delete}'
                                ],
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
