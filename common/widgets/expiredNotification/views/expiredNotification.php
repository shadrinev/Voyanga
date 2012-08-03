<?php
/**
* @var string $widget
* @var string $header
* @var string $message
* @var bool $showCancel
* @var string $modalId
* @var array $modalOptions
**/
?>
<?php $this->beginWidget('bootstrap.widgets.BootModal', array(
    'id'=>$modalId,
    'options'=>$modalOptions['options'],
    'events'=>$modalOptions['events'],
    'htmlOptions'=>$modalOptions['htmlOptions']
)); ?>

<?php if ($header): ?>
<div class="modal-header">
        <?php if ($showCancel): ?>
                <a class="close" data-dismiss="modal">&times;</a>
        <?php endif ?>
        <h3><?php echo $header ?></h3>
</div>
<?php endif; ?>


<div class="modal-body">
    <?php if ($message): ?>
        <?php echo $message ?>
    <?php endif ?>
</div>

<?php if ($showCancel): ?>
    <div class="modal-footer">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
        'label'=>'Close',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>
    </div>
<?php endif ?>


<?php $this->endWidget(); ?>