<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Dealers */


$this->title = $dealer->name;
$this->params['breadcrumbs'][] = ['label' => 'Dealers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dealers-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $dealer->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $dealer->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $dealer,
        'attributes' => [
            'id',
            'name',
            'code',
        ],
    ]) ?>
    
    <?= GridView::widget([
        'dataProvider' => $properties,
    ]) ?>

</div>
