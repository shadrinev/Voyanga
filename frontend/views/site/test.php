<?php
$this->pageTitle=Yii::app()->name . ' - Test';
$this->breadcrumbs=array(
	'Login',
);
?>

<h1>Login</h1>

<p>Please fill out the following form with your login credentials:</p>

<div class="form">
<?php //echo $sText?>
eaaaaРусский ййй

<?php $stack=$this->beginWidget('ext.EFlightVoageStackWidget');
//$stack_behavior = new FlightStackStarategyPrice();
$stack->attachBehavior('price',array(
 'class'=>'ext.FlightStackStrategyPrice',
 'sortKey'=>'price',
 ));
$stack->setFlightStack($flightStack);

//echo $stack->data;
/*, array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));*/ ?>
 eddsfdsf
 
<?php $this->endWidget();?>
</div><!-- form -->
