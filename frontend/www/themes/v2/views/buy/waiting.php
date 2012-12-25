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
  </body>
</html>
