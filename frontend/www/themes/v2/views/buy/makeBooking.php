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
                            <div class="cityPoint last" style="right: 42px;" data-bind='text: $data.cityName'>
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
        <div class="agreeConditions">
           <label for="agreeCheck">
               <input type="checkbox" data-bind="checkbox:{label: 'Я согласен с <a href=\'/agreement_avia\' target=\'_blank\'>условиями использования</a>,<br><a href=\'/iata\' target=\'_blank\'>правилами IATA</a> и правилами тарифов', checked: 0}" name="agree" id="agreeCheck">
           </label>
        </div>
        <div class="btnBlue inactive" id='submit-passport'>
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
    <div class="payCardPal" style="display: none;">
        <div class="centerBlock">
            <table class="headerTitle">
                <tr>
                    <td class="h1">Оплатить <span class="grey">или</span> <a href="javascript:javascript:history.go(-1)">вернуться к выбору вариантов</a> </td>
                    <td class="time">Ваши билеты забронированы, необходимо оплатить за 1 ч. 58 м.</td>
                </tr>
            </table>
            <table class="mainTable">
                <tr>
                    <td class="tdInfoText">
                        Сумма может быть списана в несколько транзакций. <span class="whyQuest tooltipClose" rel="Это сделано для удобства и экономии ваших денег. На каждую составляющую вашей поездки мы делаем отдельное списание, а данные платежной карточки вы вводите один раз. Это позволяет экономить: мы меньше тратим на эквайринг, вы - получаете более привлекательные цены. Так же это дает дополнительное удобство в случае, если после оплаты всего заказа вы решите отказаться только от отеля, не отменяя авиабилет. В этом случае нам не надо будет отменять весь заказ целиком.">Почему?</span>

                        <table class="aviaAllPrice">
                            <thead>
                                <tr>
                                    <td colspan="2">Перелет LED - MOW:</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="price"><div>5 500 <span class="rur">o</span></div></td>
                                    <td class="text">тариф и таксы</td>
                                </tr>
                                <tr>
                                    <td class="price"><div>5 500 <span class="rur">o</span></div></td>
                                    <td class="text">тариф и таксы</td>
                                </tr>
                            </tbody>

                        </table>

                        <table class="aviaAllPrice">
                            <thead>
                            <tr>
                                <td colspan="2">Перелет MOW - LED:</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="price"><div>5 500 <span class="rur">o</span></div></td>
                                <td class="text">тариф и таксы</td>
                            </tr>
                            <tr>
                                <td class="price"><div>5 500 <span class="rur">o</span></div></td>
                                <td class="text">тариф и таксы</td>
                            </tr>
                            </tbody>

                        </table>

                        <table class="hotelAllPrice">
                            <tbody>
                            <tr>
                                <td class="price"><div>14 500 <span class="rur">o</span></div></td>
                                <td class="text">гостиницы</td>
                            </tr>
                            </tbody>

                        </table>
                    </td>
                    <td class="tdIframe">
                        <iframe id="payment_frame" name="payment_frame" class="payCardPaliFrame"></iframe>
                    </td>
                </tr>
            </table>

        </div>
    </div>
    <div class="paybuyEnd" style="display: none">
        <div class="info">После нажатия кнопки «Оплатить» данные пассажиров попадут в систему бронирования, билет будет
            оформлен и выслан вам на указанный электронный адрес в течение нескольких минут. Статус заказа всегда можно 
            увидеть в личном кабинете.
        </div>
        <div class="clear"></div>
    </div>
</div>
