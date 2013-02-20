<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerCssFile('/themes/v2/css/style.css');

?>
<html>
  <head>
  </head>
  <body class="whiteBody">

        <div id="payments-waiting">
            <h2>Ожидаем ответа платежной системы</h2>
            <div class="text">
                Мы обрабатываем платеж, это может занять несколько секунд. Не обновляйте страницу.
            </div>
        </div>
        <div id="payments-ok" style="display:none;">
          <h2>Платеж обработан, ожидайте билеты</h2>
            <div class="text">
                В течении двух минут на указанный email мы вышлем выписанные билеты, так же статус заказа можно отслеживать в личном кабинете. Если через 15 минут вы так и не получите электронного письма с подтверждением, пожалуйста, свяжитесь с нашим отделом обслуживания клиентов по телефону +7&nbsp;(499)&nbsp;553-09-33, мы отправим ваши билеты ещё раз.<br><br>
                Перейти <a href="#" onclick="top.window.location.href='/';">на главную</a>
            </div>
        </div>
        <div id="payments-fail" style="display:none;">
          <h2>Ошибка обработки платежа</h2>
            <div class="text">
                Платеж отклонен. Возможно, данные банковской карты были введены некорректно, либо банк отклонил платеж по причине недостаточного количества средств на карте. Если с вашей карты были списаны деньги, то они автоматически вернутся в течении часа.<br>
                Если это не так, пожалуйста, свяжитесь с нашим отделом обслуживания клиентов по телефону +7&nbsp;(499)&nbsp;553-09-33.<br><br>
                Перейти <a href="#" onclick="top.window.location.href='/';">на главную</a>
            </div>

        </div>

    <script type="text/javascript">
    $(function(){
      "use strict";
      var item, done=false;
      var blocks = ['waiting', 'show', 'fail'];
      var show = function(id) {
        for(var i=0; i < blocks.length; i++) {
          item = blocks[i];
          $('#payments-' + item).hide(); 
        };
        $('#payments-' + id).show();
        console.log("JAJAJA", '#payments-' + id);
      };
      if(window.location.hash=='#ok')
      {
         show('ok');
         return;
      }
      if(window.location.hash=='#fail')
      {
         show('fail');
         return;
      }

      if(window.location.hash=='#waiting')
      {
         show('wait');
         return;
      }
      function poll()
      {
        if(done)
          return;
        setTimeout(function(){
          $.get('/buy/paymentStatus')
            .always(poll)
            .done(function(data){
              if(data.paid) {
                show('ok');
                done = true;
                return;
              }
              if(data.error) {
                show('fail');
              }
            })
            .fail(function(){
              show('fail');
            });
        }, 5000);
      }
      poll();
    });
    </script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38508830-1']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_addTrans',
    '<?= $order->getOrderBooking()->id ?>',           // order ID - required
    'Voyanga.com',  // affiliation or store name
    '<?= $order->getOrderBooking()->fullPrice ?>',          // total - required
  ]);

   // add item might be called for every item in the shopping cart
   // where your ecommerce engine loops through each item in the cart and
   // prints out _addItem for each
  <?php foreach($order->getBookers() as $booker): ?>
  _gaq.push(['_addItem',
    '<?= $order->getOrderBooking()->id ?>',           // order ID - required
    '<?= $booker->getSKU() ?>',           // SKU/code - required
    '<?= isset($booker->getCurrent()->hotel)?$booker->getCurrent()->hotel->rubPrice:$booker->getCurrent()->price ?>',          // unit price - required
    '1'               // quantity - required
  ]);
  <?php endforeach; ?>
  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


<!-- Google Code for &#1050;&#1086;&#1088;&#1079;&#1080;&#1085;&#1072; Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 993261133;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "sWalCPPuqQQQzezP2QM";
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/993261133/?value=0&amp;label=sWalCPPuqQQQzezP2QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

  </body>
</html>
