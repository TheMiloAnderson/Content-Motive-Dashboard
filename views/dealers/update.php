<?php

use yii\helpers\Html;
use app\assets\DealerFormAssets;

/* @var $this yii\web\View */
/* @var $dealer app\models\Dealers */

DealerFormAssets::register($this);

$this->title = 'Update Dealers: ' . $dealer->name;
$this->params['breadcrumbs'][] = ['label' => 'Dealers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $dealer->name, 'url' => ['view', 'id' => $dealer->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="dealers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'dealer' => $dealer,
        'properties' => $properties,
    ]) ?>

</div>