<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;
use app\modules\rbac\widgets\AddPrem;
$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>
    <?php if(isset($exception->statusCode) && $exception->statusCode == 403):?>
        <?= AddPrem::widget()?>
    <?php endif;?>

</div>
