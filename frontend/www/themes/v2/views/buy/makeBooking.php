<script type="text/javascript">
    <?php $tripRaw = 'window.tripRaw = ' . $trip; ?>
    <?php echo $tripRaw ?>;
    window.currentModule = '<?php echo Yii::app()->user->getState('currentModule'); ?>';
    $(function () {
        initCredentialsPage();
    })
</script>
<script id="tariff-rules-template" type="text/html">

    <div id="tariff-rules" class="body-popup">
        <div id="layer">
            <div class="pv_cont error">
                <table cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <div id="pv_box">
                                <div class="contentText">
                                    <div>
                                        <h1 >Правила тарифов</h1>
                                        <div data-bind="foreach: data.tariffs" style="color:#000; height: 40px; overflow: scroll">
                                            <div class="tariff-route" data-bind="text: route"></div>
                                            <div class="tariff-codes">
                                                <div data-bind="foreach: codes">
                                                    <span data-bind="text: code + '. '+ name"></span>
                                                    <pre data-bind="text: content"></pre>
                                                </div>
                                            </div>
                                        </div>
                                        <p align="center">
                                            <a href="#" class="btnBackMain" style="margin-top:40px"
                                               data-bind="click: close">Закрыть</a>
                                        </p>
                                    </div>
                                </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="pv_switch">
            </div>
        </div>
    </div>
</script>
<div id="content">
    <?php $this->renderPartial('_items', array('orderId'=>$orderId)); ?>
    <form method="post" id="passport_form">
        <?php if ($ambigousPassports): ?>
            <?php $this->renderPartial('_ambigousPassports', array(
                'passportForms' => $passportForms,
                'headers'=>$headersForAmbigous,
                'roomCounters'=>$roomCounters,
                'hide' => false
            )); ?>
        <?php else: ?>
            <?php $this->renderPartial('_simplePassport', array(
                'passportForms' => $passportForms,
                'icon'=>$icon,
                'header'=>$header,
                'roomCounters'=>$roomCounters,
                'hide' => false
            ));
            ?>
        <?php endif;?>
        <?php $this->renderPartial('_buyer', array('model' => $bookingForm, 'hide'=>false)); ?>
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
               <input type="checkbox" data-bind="checkbox:{label: 'Я согласен с <a href=\'/agreement\' target=\'_blank\'>условиями использования</a>,<br><a href=\'/iata\' target=\'_blank\'>правилами IATA</a> и <a onclick=\'window.app.itemsToBuy.showTariffRules()\'>правилами тарифов</a>', checked: 0}" name="agree" id="agreeCheck">
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
                    <td class="time"></td>
                </tr>
            </table>
            <table class="mainTable">
                <tr>
                    <td class="tdInfoText">
                        Сумма может быть списана в несколько транзакций. <span class="whyQuest tooltipClose" rel="Это сделано для удобства и экономии ваших денег. На каждую составляющую вашей поездки мы делаем отдельное списание, а данные платежной карточки вы вводите один раз. Это позволяет экономить: мы меньше тратим на эквайринг, вы - получаете более привлекательные цены. Так же это дает дополнительное удобство в случае, если после оплаты всего заказа вы решите отказаться только от отеля, не отменяя авиабилет. В этом случае нам не надо будет отменять весь заказ целиком.">Почему?</span>
                      <!-- ko foreach: breakdown -->
                        <table class="aviaAllPrice" data-bind="css: {aviaAllPrice: !isHotel, hotelAllPrice: isHotel}">
                            <thead data-bind="if: !isHotel">
                                <tr>
                                    <td colspan="2" data-bind="text: title">Перелет LED - MOW:</td>
                                </tr>
                            </thead>
                            <tbody data-bind="foreach:transactions">
                                <tr>
                                    <td class="price"><div><span data-bind="text: price">5 500</span> <span class="rur">o</span></div></td>
                                    <td class="text" data-bind="text: title">тариф и таксы</td>
                                </tr>
                            </tbody>

                        </table>
                        <!-- /ko -->
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
