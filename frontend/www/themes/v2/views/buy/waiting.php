<?php
Yii::app()->clientScript->registerCoreScript('jquery');
?>
<html>
  <head>
  </head>
  <body>
    <div id="payments-waiting">
      <h1>Ждем платежки</h1>
    </div>
    <div id="payments-ok" style="display:none;">
      <h1>Все свободны, ждите билеты</h1>
    </div>
    <div id="payments-fail" style="display:none;">
      <h1>Все свободны, все зафейлилось</h1>
    </div>
    <script type="text/javascript">
    $(function(){
      "use strict";
      var item;
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
        setTimeout(function(){
          $.get('/buy/paymentStatus')
            .always(poll)
            .done(function(data){
              if(data.paid) {
                show('ok');
              }
              if(data.error) {
                show('fail');
              }
      
            })
            .fail(function(){
              show('fail');

            });
        }, 10000);
      }
      poll();
    });
    </script>
  </body>
</html>
