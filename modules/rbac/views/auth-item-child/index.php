<?php

use app\models\AuthItemChild;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\AuthItemChildSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Наследование ролей и разрешений';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-child-index">
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

                                'parent',
                                'child',
                                [
                                    'class' => ActionColumn::className(),
                                    'urlCreator' => function ($action, AuthItemChild $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'parent' => $model->parent, 'child' => $model->child]);
                                    },
                                    'template' => '{delete}'
                                ],
                            ],
                        ]); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
