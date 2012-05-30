<?php
/**
 * The input form for the {@link AAuthOperation} model
 * @var AAuthOperation $model The AAuthOperation model
 */
$lookupUrl = json_encode($this->createUrl("findRoute"));
$script = <<<JS
$("#AAuthOperation_name").bind("change",function(e){
	if ($(this).val().substr(0,1) === "/") {
		// this could be a route, let's look it up
		$.ajax({
			"url": {$lookupUrl},
			"data": $("#aauth-operation-form").serialize(),
			"type": "POST",
			"success": function(res) {
				$("#AAuthOperation_description").val(res.comment);
			}
		});
	}
});
JS;
Yii::app()->clientScript->registerScript("routeFinder", $script);
?>
<div class="form">

    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id' => 'aauth-operation-form',
    'enableAjaxValidation' => true,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->textFieldRow($model, 'name', array('size' => 60, 'maxlength' => 64)); ?>
      <p class='hint'>Please enter a unique name for this operation. E.g. 'createBlogPost' or '/blog/post/create' to
        mark the operation as an URL route.</p>
    </div>

    <?php echo $form->textAreaRow($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
    <p class='hint'>Please enter a short description for this operation</p>

    <?php echo $form->textArea($model, 'bizrule', array('rows' => 6, 'cols' => 50)); ?>
    <p class='hint'>Here you can enter a <b>valid</b> PHP expression that determines whether this operation really applies.</p>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
    )); ?>
    </div>
<?php $this->endWidget(); ?>