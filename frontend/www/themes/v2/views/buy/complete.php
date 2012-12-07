<script type="text/javascript">
    <?php $tripRaw = 'window.tripRaw = ' . $trip; ?>
    <?php echo $tripRaw ?>;
    window.currentModule = '<?php echo Yii::app()->user->getState('currentModule'); ?>';
    $(function () {
        initCompletedPage();
        console.log(window.tripRaw);
    })
</script>
<div id="content">
    <?php $this->renderPartial('_completedItems', array('orderId'=>$readableOrderId)); ?>
</div>