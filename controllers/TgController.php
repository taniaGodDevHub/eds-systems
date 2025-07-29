<?php

namespace app\controllers;


use Yii;

class TgController extends AccessController
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['Authorization'],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $postData = json_decode(file_get_contents('php://input'));
        Yii::info("Вебхук от ТG" . print_r($this->request->get(), true). print_r($postData,true), 'tg');
        return true;
    }
}
