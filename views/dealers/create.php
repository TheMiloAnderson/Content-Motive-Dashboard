<?php

use yii\helpers\Html;
use app\assets\DealerFormAssets;


/* @var $this yii\web\View */
/* @var $model app\models\Dealers */

DealerFormAssets::register($this);

$this->title = 'Create Dealers';
$this->params['breadcrumbs'][] = ['label' => 'Dealers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dealers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'dealer' => $dealer,
        'properties' => $properties,
    ]) ?>

</div>
