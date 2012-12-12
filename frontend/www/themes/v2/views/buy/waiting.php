<?php
Yii::app()->clientScript->registerCoreScript('jquery');
?>
<html>
<head>
</head>
<body>
<div>
<h1 id="payments-message">Ждем платежки</h1>
</div>
<script type="text/javascript">
  $(function(){
    function poll()
    {
      setTimeout(function(){
        $.get('/buy/paymentStatus').always(poll).done(function(data){
          if(data.paid)
            $('#payments-message').text("И тут такой тикетинг врывается(на самом деле нет)");

        });
      }, 10000);
    }
    poll();
  });
</script>
</body>
</html>
