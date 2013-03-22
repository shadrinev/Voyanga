<script type="text/javascript">
    <?php $tripRaw = 'window.tripRaw = ' . $trip; ?>
    <?php echo $tripRaw ?>;
    window.currentModule = '<?php echo Yii::app()->user->getState('currentModule'); ?>';
    $(function () {
        initCredentialsPage();
    })
</script>
<script id="tariff-rules-template" type="text/html">
    <div id="tariff-rules" style="display: block;" class="contentWrapBg">
        <div class="wrapDiv">
            <div class="wrapContent">
                <h1 >Правила тарифов</h1>
                <div data-bind="foreach: data.tariffs" style="color:#000;">
                    <div onclick="nextSlideDownRules(this);" class="tariff-route" data-bind="text: route"></div>
                    <div class="tariff-codes" style="display:none;">
                        <div class="tariffStyle" data-bind="foreach: codes">
                            <span data-bind="text: code + '. '+ name"></span>
                            <pre data-bind="text: content"></pre>
                        </div>
                    </div>
                </div>
                <div data-bind="click: close" class="boxClose"></div>
            </div>
        </div>
    </div>

</script>
<div id="content" style="overflow: hidden;">
    <div class="panelBuy">
        <div class="center-block">
            <table>
                <tr>
                    <td style="width: 150px" class="allVariantsBlock">
                        <a href="#" class="btn-allVariantion"><span class="ico-list"></span> Все варианты <span class="l"></span></a>
                    </td>
                    <td>
                        <div class="oneString">
                            Санкт-Петербург ↔ Москва
                        </div>
                    </td>
                    <td style="width: 150px">
<!--                        <a href="#">Новый поиск</a>-->
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="lineUp">
        <a href="#" class="btn-close" style="display: none"></a>
        <div class="center-block">
            <table class="advantageTable">
                <tr>
                    <td class="first">
                        <strong>Почему <span class="text-ticket1">билеты</span><br>
                        покупают у нас?</strong>
                    </td>
                    <td>
                        <span class="ico-price"></span>
                        Окончательная цена,<br>
                        нет комиссий
                    </td>
                    <td>
                        <span class="ico-secqury"></span>
                        Безопасность платежей,<br>
                        международные стандарты
                    </td>
                    <td>
                        <span class="ico-time"></span>
                        Быстрое оформление,<br>
                        <span class="text-ticket2">билет</span> ваш через 2 минуты
                    </td>
                    <td>
                        <span class="ico-helps"></span>
                        Помощь в решении<br>
                        любых проблем
                    </td>
                </tr>
            </table>
        </div>
    </div>
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
               <input type="checkbox" data-bind="checkbox:{label: 'Я согласен с <a href=\'/agreement\' target=\'_blank\'>условиями использования</a>,<br><a href=\'/iata\' target=\'_blank\'>правилами IATA</a> и <a onclick=\'window.app.itemsToBuy.showTariffRules()\' href=\'#\'>правилами тарифов</a>', checked: 1}" name="agree" id="agreeCheck">
           </label>
        </div>
        <div class="pressButton inactive" id='submit-passport'>
            <span>Перейти к оплате</span>&nbsp;&nbsp;
            <span class="price"data-bind="text: Utils.formatPrice(itemsToBuy.totalCost)">33 770</span>
            <span class="rur">o</span>
            <span class="l"></span>
        </div>
        <div class="armoring" style="display: none">
            <div class="pressButton">
                <span>Бронирование</span>

                <div class="dotted"></div>
                <span class="l"></span>
            </div>
            <div class="text">
                Подождите, бронирование займет от 5 до 30 секунд
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <!--=== ===-->
    <div class="payCardPal" style="display: none;">
        <div class="centerBlock">
            <table class="headerTitle">
                <tr>
                    <td class="h1">Оплатить <span class="grey">банковской картой или</span> <a href="javascript:javascript:history.go(-1)">вернуться к выбору вариантов</a> </td>
                    <td class="time">
                       Необходимо оплатить в течение <strong>1 часа</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="line-height: 18px;">Ваши билеты готовы и будут отправлены по электронной почте сразу после оплаты.</span>
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <table class="mainTable">
                <tr>
                    <td class="tdInfoText" data-bind="if: breakdown.length">
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
                                    <td class="price"><div><span data-bind="text: Utils.formatPrice(price)">5 500</span> <span class="rur">o</span></div></td>
                                    <td class="text" data-bind="text: title">тариф и таксы</td>
                                </tr>
                            </tbody>

                        </table>
                        <!-- /ko -->
                    </td>
                    <td class="tdIframe" data-bind='attr: {style: breakdown.length?"":"text-align: center; width: 100%"}'>
                        <div class="iframeDiv">
                            <iframe id="payment_frame" name="payment_frame" class="payCardPaliFrame"></iframe>
                        </div>
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
