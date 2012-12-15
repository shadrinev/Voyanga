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
    <?php $this->renderPartial('_completedItems', array('orderId' => $readableOrderId)); ?>
</div>
<script type="text/javascript">
    var initDate = new Date(); // Or get the user login date from an HTML element (i.e. hidden input)
    var interval;

    function keepAlive() {
        $('#updateStatus').fadeIn();
        $.ajax({
            url: '/buy/status/id/<?php echo $secretKey ?>',
            dataType: 'json'
        })
        .done(function(response){
            _.each(window.tripRaw.items, function(el, i){
                console.log("Element: ");
                console.log(el);
                var ind = el.key,
                    newStatus = response[ind];
                $('#'+ind).text(newStatus);
            });
            $('#updateStatus').fadeOut();
        })
        .error(function(){
            $('#updateStatus').fadeOut();
        });
    }

    window.onload = function () {
        keepAlive();

        interval = window.setInterval(function () {
            var now = new Date();
            if (now.getTime() - initDate.getTime() < 1 * 60 * 60 * 1000 && now.getDate() == initDate.getDate()) {
                keepAlive();
            }
            else {
                // Stop the interval
                window.clearInterval(interval);
            }
        }, 15 * 1000);
    }
</script>