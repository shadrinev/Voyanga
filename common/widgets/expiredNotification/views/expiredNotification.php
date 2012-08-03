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

<div class="modal-header">
    <?php if ($header): ?>
        <?php if ($showCancel): ?>
                <a class="close" data-dismiss="modal">&times;</a>
        <?php endif ?>
        <h3><?php echo $header ?></h3>
    <?php endif; ?>
</div>

<div class="modal-body">
    <?php if ($message): ?>
        <?php echo $message ?>
    <?php endif ?>
</div>

<div class="modal-footer">
    <?php if ($showCancel): ?>
        <?php $this->widget('bootstrap.widgets.BootButton', array(
        'label'=>'Close',
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>
    <?php endif ?>
</div>

<?php $this->endWidget(); ?>