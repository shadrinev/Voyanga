<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('requestNum')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->requestNum),array('view','id'=>$data->requestNum)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('methodName')); ?>:</b>
	<?php echo CHtml::encode($data->methodName); ?>
	<br />

</div>