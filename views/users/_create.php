<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'admin')
                    ->checkbox(['data-toggle' => 'toggle', 'label' => NULL])
                    ->label('Admin? <br />', ['style' => 'display:block;']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>  
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="userswithdealers-email">Password</label>
                    <?= Html::textInput('UsersWithDealers[newPassword]', null, [
                        'class' => 'form-control',
                    ]) ?>
                    <div class="help-block"></div>
                </div>
            </div>    
        </div>
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
    </div>
    <?php ActiveForm::end(); ?>

</div>