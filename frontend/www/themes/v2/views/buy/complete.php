<script type="text/javascript">
    <?php $tripRaw = 'window.tripRaw = ' . $trip; ?>
    <?php echo $tripRaw ?>;
    window.currentModule = '<?php echo Yii::app()->user->getState('currentModule'); ?>';
    window.orderId = '<?php echo $secretKey ?>';
    $(function () {
        initCompletedPage();
    })
</script>
<div id="content">
    <?php $this->renderPartial('_completedItems',
        array(
            'orderId' => $readableOrderId,
        )); ?>
</div>
<form method="post" id="passport_form">
    <?php if ($ambigousPassports): ?>
        <?php $this->renderPartial('_ambigousPassports', array('passportForms' => $passportForms, 'headers'=>$headersForAmbigous, 'roomCounters'=>$roomCounters)); ?>
    <?php else: ?>
        <?php $this->renderPartial('_simplePassport', array('passportForms' => $passportForms, 'icon'=>$icon, 'header'=>$header, 'roomCounters'=>$roomCounters)); ?>
    <?php endif;?>
    <?php $this->renderPartial('_buyer', array('model' => $bookingForm)); ?>
</form>