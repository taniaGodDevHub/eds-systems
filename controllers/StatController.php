<?php

namespace app\controllers;

use app\controllers\AccessController;
use app\models\AuthAssignment;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\ManagerToChat;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

class StatController extends AccessController
{
    public function actionIndex()
    {
        $managers = ManagerToChat::find()
            ->with(['managerProfile', 'manager'])
            ->distinct('manager_id')
            ->asArray()
            ->all();
        echo "<pre>";
        print_r($managers);die;
        return $this->render('index');
    }
}