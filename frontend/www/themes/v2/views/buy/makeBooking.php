<script type="text/javascript">
    <?php $tripRaw = 'window.tripRaw = '.$trip; ?>
    <?php echo $tripRaw ?>;
    window.currentModule = '<?php echo Yii::app()->user->getState('currentModule'); ?>';
</script>
<div id="content">
        <?php $this->renderPartial('_items'); ?>
        <form method="post" id="passport_form">
            <?php if($ambigousPassports): ?>
                <?php $this->renderPartial('_ambigousPassports', array('passportForms'=>$passportForms)); ?>
            <?php else: ?>
                <?php $this->renderPartial('_simplePassport', array('passportForms'=>$passportForms)); ?>
            <?php endif;?>
            <?php $this->renderPartial('_buyer', array('model'=>$bookingForm)); ?>
            <div class="paybuyEnd">
                <div class="btnBlue" id="submit-passport">
                    <span>OK</span>
                </div>
                <div class="clear"></div>
            </div>
        </form>
     <script type="text/javascript">
       $(function(){
        $('#submit-passport').click(function(){
         $.post('/buy/makeBooking', $('#passport_form').serialize(), function(data){
           if(data.status == 'success') {
             $.get('/buy/startPayment', function(data) {
                     if(data.error){
                         throw "Payment error";
                     } else {
                         Utils.submitPayment(data);
                     }
             });
           } else {
             alert("ERROR" + data);
           }
         });
        });
       });
     </script>
		<!--=== ===-->
		<div class="payCardPal">
		  <iframe id="payment_frame" class="payCardPal"></iframe>
		</div>
		<div class="paybuyEnd">
			<div class="info">После нажатия кнопки «Купить» данные пассажиров попадут в систему бронирования, билет будет оформлен и выслан вам на указанный электронный адрес в течение нескольких минут. Нажимая «Купить», вы соглашаетесь с условиями использования, правилами IATA и правилами тарифов.</div>
			<div class="clear"></div>
		</div>
		<div class="paybuyEnd">
				<div class="btnBlue">
					<span>Забронировать</span>&nbsp;&nbsp;
					<span class="price">33 770</span> 
					<span class="rur">o</span>
					
					<span class="l"></span>
				</div>
			<div class="clear"></div>
		</div>
		<div class="paybuyEnd">
			<div class="armoring">
				<div class="btnBlue">
					<span>Бронирование</span>
					<div class="dotted"></div>
					<span class="l"></span>
				</div>
				<div class="text">
					Процесс бронирования может занять до 45 секунд...
				</div>
			</div>
			<div class="clear"></div>
		</div>
</div>
