<?php

namespace app\controllers;


use Yii;

class TgController extends AccessController
{
    public function actionIndex()
    {
        Yii::info("Вебхук от ТG" . print_r($this->request->get(), true), 'tg');
        return true;
    }
}
