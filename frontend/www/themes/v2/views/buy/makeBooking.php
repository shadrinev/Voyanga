<script type="text/javascript">
    <?php $tripRaw = 'window.tripRaw = ' . $trip; ?>
    <?php echo $tripRaw ?>;
    window.currentModule = '<?php echo Yii::app()->user->getState('currentModule'); ?>';
    $(function () {
        initCredentialsPage();
    })
</script>
<div id="content">
    <?php $this->renderPartial('_items', array('orderId'=>$orderId)); ?>
    <form method="post" id="passport_form">
        <?php if ($ambigousPassports): ?>
            <?php $this->renderPartial('_ambigousPassports', array('passportForms' => $passportForms, 'headers'=>$headersForAmbigous, 'roomCounters'=>$roomCounters)); ?>
        <?php else: ?>
            <?php $this->renderPartial('_simplePassport', array('passportForms' => $passportForms, 'icon'=>$icon, 'header'=>$header, 'roomCounters'=>$roomCounters)); ?>
        <?php endif;?>
        <?php $this->renderPartial('_buyer', array('model' => $bookingForm)); ?>
    </form>
    <div class="paybuyEnd" id="loadPayFly">
        <div class="loadJet" style="display: none">
            <div class="pathBlock">
                <div class="overflowBlock">
                    <div class="linePath"></div>
                    <!-- ko foreach: itemsToBuy.cities -->
                        <!-- ko if: $data.isLast -->
                            <div class="cityPoint" style="right: 42px;" data-bind='text: $data.cityName'>
                                Санкт-Петербург
                            </div>
                        <!-- /ko -->
                        <!-- ko ifnot: $data.isLast -->
                            <div class="cityPoint" style="left: 5%;" data-bind='style: {left: $data.left}, text: $data.cityName'>
                                Санкт-Петербург
                            </div>
                        <!-- /ko -->
                    <!-- /ko -->
                </div>
            </div>
            <div class="jetFly"></div>
            <div class="bgFinish"></div>
            <div class="bgGradient"></div>
        </div>
        <div class="btnBlue" id='submit-passport'>
            <span>Забронировать</span>&nbsp;&nbsp;
            <span class="price"data-bind="text: itemsToBuy.totalCost">33 770</span>
            <span class="rur">o</span>
            <span class="l"></span>
        </div>
        <div class="armoring" style="display: none">
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
    <!--=== ===-->
    <div class="payCardPal" style="display: none">
        <iframe id="payment_frame" class="payCardPal"></iframe>
    </div>
    <div class="paybuyEnd" style="display: none">
        <div class="info">После нажатия кнопки «Купить» данные пассажиров попадут в систему бронирования, билет будет
            оформлен и выслан вам на указанный электронный адрес в течение нескольких минут. Нажимая «Купить», вы
            соглашаетесь с условиями использования, правилами IATA и правилами тарифов.
        </div>
        <div class="clear"></div>
    </div>
</div>