<?php

namespace app\controllers;


use yii\web\Controller;
use app\modules\rbac\exceptions\ForbiddenHttpException;

class AccessController extends Controller
{
    public function beforeAction($action)
    {

        $premissionName = str_replace(['/', '-'], '_', $action->uniqueId);

        if (!\Yii::$app->user->can($premissionName)
            && $premissionName != 'site_index'
            && $premissionName != 'site_signup'
            && $premissionName != 'site_login'
            && $premissionName != 'site_logout'
            && $premissionName != 'site_error'
            && $premissionName != 'site_contact'
            && $premissionName != 'site_verify-email') {
            throw new ForbiddenHttpException(
                "Войдите в систему или зарегистрируйтесь. У вас нет разрешений на доступ к этой странице.($premissionName)",
                0,
                null,
                $premissionName
            );
        }
        return parent::beforeAction($action);
    }
}
