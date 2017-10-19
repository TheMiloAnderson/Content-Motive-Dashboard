<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin')->checkbox(['data-toggle' => 'toggle', 'label' => NULL])->label('Admin? &nbsp;') ?>

    <?= $form->field($model, 'dealers')->checkboxList($allDealers, ['separator' => "<br />", 
        'item' => function($index, $label, $name, $checked, $value) {
            return "<label><input type='checkbox' " . ($checked == 1 ? "checked=''" : "") . " name='{$name}' value='{$value}'> {$label} "
            . Html::a('<span class="glyphicon glyphicon-edit"></span>', ['dealers/update', 'id' => $value], ['target' => '_blank']) . "</label>";
        }])
        ->label('Dealers<br /><a type="button" class="btn btn-sm btn-default" id="dealer-list-all">All</a> '
        . '<a type="button" class="btn btn-sm btn-default" id="dealer-list-none">None</a> ') ?>

    <div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>