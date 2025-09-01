<?php

namespace app\controllers;

use app\controllers\AccessController;
use app\models\Activity;
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

class ActivityController extends AccessController
{
    public function actionSetActive($user_id)
    {
        $unactivePeriod = 900;

        $a = Activity::find()
        ->where(['user_id' => $user_id])
        ->orderBy(['id' => SORT_DESC])
        ->one();

        if(empty($a)){
            $a = new Activity();
            $a->user_id = $user_id;
            $a->start_date = time();
            $a->end_date = time();
            $a->save();
        }else{

            if((time() - $a->end_date) < $unactivePeriod){

                $a->end_date = time();
                $a->save();
            }else{

                $a = new Activity();
                $a->user_id = $user_id;
                $a->start_date = time();
                $a->end_date = time();
                $a->save();
            }
        }

        return $this->asJson(true);
    }
}