<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $dealer app\models\Dealers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dealers-form">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="panel panel-default">
        <div class="panel-heading"><h4><?= $form->field($dealer, 'name')->textInput(['maxlength' => true]) ?></h4></div>
        <div class="panel-body">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $properties[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'url',
                    'ga_view',
                    'start_date',
                    'type',
                ],
            ]); ?>
<!--                    <div class="container-items">-->

                <div class="container-items panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">Google Analytics Properties</h3>
                        <div class="pull-right">
                            <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>

                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <?php foreach ($properties as $i => $prop) : ?>
                    <div class="item panel-body">
                        <div class="row">
                            <div class="col-sm-5">
                                <?php if (!$prop->isNewRecord) {
                                    echo Html::activeHiddenInput($prop, "[{$i}]id");
                                } ?>
                                <?= $form->field($prop, "[{$i}]url")->textInput(['max-length' => true]); ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($prop, "[{$i}]ga_view")->textInput(['max-length' => true]); ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($prop, "[{$i}]type")->textInput(['max-length' => true]); ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($prop, "[{$i}]start_date")->textInput(['max-length' => true]); ?>
                            </div>
                            <div class="col-sm-1">
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>                            
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

<!--                    </div>-->
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>
        
    
    <div class="form-group">
        <?= Html::submitButton($dealer->isNewRecord ? 'Create' : 'Update', ['class' => $dealer->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
