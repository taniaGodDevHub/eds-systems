<?php

namespace app\modules\rbac\controllers;

use app\controllers\AccessController;
use app\models\AuthItem;
use app\models\AuthItemChild;
use Yii;
/**
 * Default controller for the `rbac` module
 */
class DefaultController extends AccessController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAddPrem(){
        if(!\Yii::$app->request->isPost){
            \Yii::$app->session->setFlash("danger", "Нельзя отправлять такие запросы.");
            if (\Yii::$app->request->referrer) {
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                return $this->goHome();
            }
        }

        $auth = \Yii::$app->authManager;

        $prem = $auth->getPermission(\Yii::$app->request->post('premissionName'));

        if(empty($prem)){
            $prem = $auth->createPermission(\Yii::$app->request->post('premissionName'));
            $prem->description = \Yii::$app->request->post('premissionName');
            $auth->add($prem);
        }

        $role = $auth->getRole(\Yii::$app->request->post('role'));

        if(empty($role)){
            \Yii::$app->session->setFlash("danger", "Не удалось получить роль для назначения прав.");
            if (\Yii::$app->request->referrer) {
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                return $this->goHome();
            }
        }

        $auth->addChild($role, $prem);

        \Yii::$app->session->setFlash("success", "Разрешение " . \Yii::$app->request->post('premissionName') . " назначено роли ". \Yii::$app->request->post('role'));

        if (\Yii::$app->request->referrer) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->goHome();
        }
    }
}
