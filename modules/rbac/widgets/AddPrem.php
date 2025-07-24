<?php

namespace app\modules\rbac\widgets;

use Yii;
use app\models\AuthItem;
class AddPrem extends \yii\bootstrap5\Widget
{
    public function run()
    {
        $list = AuthItem::getList();
        return $this->render('add-prem', compact('list', 'premissionName'));
    }
}
