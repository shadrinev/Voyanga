<?php
$images = '/themes/v2';
$url = $shortUrl;
$image = Yii::app()->createAbsoluteUrl('/') . '/themes/v2/images/mini-loading.png' ;

if (mb_strlen($title) > 130)
{
    $titleTwitter = mb_substr($title, 0, 127) . '...';
}
else
{
    $titleTwitter = $title;
}

$shortDescription = mb_substr($description, 0, 197);
if (mb_strlen($description)>197)
    $shortDescription .= '...';

// Set opengraph meta tags
$cs = Yii::app()->getClientScript();
$cs->registerMetaTag('Voyanga.com', NULL, NULL, array('property' => 'og:site_name'));
$cs->registerMetaTag('shadrinev', NULL, NULL, array('property' => 'fb:admins'));
$cs->registerMetaTag($url, NULL, NULL, array('property' => 'og:url'));
$cs->registerMetaTag($title, NULL, NULL, array('property' => 'og:title'));
$cs->registerMetaTag('article', NULL, NULL, array('property' => 'og:type'));
$cs->registerMetaTag($description, NULL, NULL, array('property' => 'og:description'));
$cs->registerMetaTag($image, NULL, NULL, array('property' => 'og:image'));

//set image src for vk.com
$cs->registerLinkTag("image_src", NULL, $image);

//set twitter cards tags
$cs->registerMetaTag('summary', NULL, NULL, array('property' => 'twitter:card'));
$cs->registerMetaTag($url, NULL, NULL, array('property' => 'twitter:url'));
$cs->registerMetaTag($title, NULL, NULL, array('property' => 'twitter:title'));
$cs->registerMetaTag($shortDescription, NULL, NULL, array('property' => 'twitter:description'));
$cs->registerMetaTag($image, NULL, NULL, array('property' => 'twitter:image'));
$cs->registerMetaTag('@voyanga', NULL, NULL, array('property' => 'twitter:site'));
?>

<script>
    window.tripRaw = <?php echo json_encode($tour); ?>;
    window.orderId = <?php echo $orderId; ?>;
    window.shortUrl = '<?php echo $url; ?>';
    $(document).ready(function () {
        initTourPage();
        $('a.addthis_button_vk').attr('addthis:url', window.shortUrl);
        $('a.addthis_button_facebook').attr('addthis:url', window.shortUrl);
        $('input[name=textTextText]').val(window.shortUrl);
        $('#socialButtons').show();
    })
</script>

<span style="display: none" id='socialSource'>
    <div id='socialButtons' style="display: none">
        <a href="javascript:void(0);" id="followLink" title="Прямая ссылка">
            <span class="text">Получить ссылку</span><span class="getLink"><input type="text" name="textTextText"
                                                                                  value=""> </span>
        </a>

        <div class="addthis_toolbox addthis_default_style addthis_32x32_style">

            <a class="addthis_button_vk"
               addthis:url=""
               addthis:title="<?php echo $title ?>"
               addthis:description="<?php echo $description;?>" title="Вконтакте"></a>
            <a class="addthis_button_facebook"
               addthis:url=""
               addthis:title="<?php echo $title ?>"
               addthis:description="<?php echo $description;?>" title="Facebook"></a>
            <a
                class="addthis_button_twitter"
                addthis:title="<?php echo $titleTwitter ?>"
                title="Twitter"></a>
        </div>
        <script>
            var addthis_share = {
                templates:{
                    twitter:"{{title}} #voyanga {{url}}"
                }
            }
        </script>
        <script type="text/javascript"
                src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51091a35689a0426"></script>

    </div>
</span>

<div class="sub-head event" data-bind="css: {calSelectedPanelActive: !itemsToBuy.activePanel().calendarHidden()}">
    <div class="board">
        <div class="constructor">
            <!-- BOARD CONTENT -->
            <div class="board-content" data-bind="with: itemsToBuy.activePanel()">
                <!-- ko template: {foreach: $data.panels, afterRender: $data.afterRender, beforeRemove: $data.beforeRemove} -->
                <!-- ko if: $index()!=0 -->
                <div class="deleteTab" data-bind="click: $parent.deletePanel"></div>
                <!-- /ko -->
                <div class="panel">
                    <table class="panelTable constructorTable">
                        <tr>
                            <td class="tdCityStart">
                                <div class="cityStart">
                                    <!-- ko if: $index()==0 || ($parent.isFirst()) -->
                                    <div class="to">
                                        Старт из:
                                        <a href="#"><span
                                                data-bind="click: showFromCityInput, text: $parent.startCityReadableGen">Санкт-Петербурга</span></a>
                                    </div>
                                    <div class="startInputTo">
                                        <div class="bgInput">
                                            <div class="left"></div>
                                            <div class="center"></div>
                                            <div class="right"></div>
                                        </div>
                                        <input type="text" tabindex="-1" class="input-path"
                                               data-bind="blur: hideFromCityInput">
                                        <input type="text" placeholder="Санкт-Петербург" class="second-path"
                                               data-bind="blur: hideFromCityInput, autocomplete: {source:'city/airport_req/1', iata: $parent.startCity, readable: $parent.startCityReadable, readableAcc: $parent.startCityReadableAcc, readableGen: $parent.startCityReadableGen, readablePre: $parent.startCityReadablePre}"
                                               autocomplete="off" style="">
                                    </div>
                                    <!-- /ko -->
                                </div>
                            </td>
                            <td class="tdCity">
                                <div class="data">
                                    <div class="from" data-bind="css: {active: checkIn()}">
                                        <div class="bgInput">
                                            <div class="left"></div>
                                            <div class="center"></div>
                                            <div class="right"></div>
                                        </div>
                                        <input type="text" placeholder="Куда едем?" class="second-path"
                                               data-bind="hasfocus: hasfocus, click: hideFromCityInput, autocomplete: {source:'city/airport_req/1', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen, readablePre: cityReadablePre}, css: {isFirst: $parent.isFirst()}"
                                               autocomplete="off">
                                        <input type="text" tabindex="-1" class="input-path">

                                        <div class="date noDate"
                                             data-bind="click: showCalendar, html:checkInHtml(), css: {'noDate': !checkIn()}"></div>
                                        <div class="date noDate"
                                             data-bind="click: showCalendar, html:checkOutHtml(), css: {'noDate': !checkOut()}"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="tdAddTour">
                                <!-- ko if: ($index()+1) == $length() -->
                                <a href="#" class="add-tour"
                                   data-bind="click: $parent.addPanel, visible: !$parent.isMaxReached()"></a>
                                <!-- /ko -->
                            </td>

                            <td class="tdPeople"
                                data-bind="css: {final: ($index()+1) == $length(), notFinal: ($index()+1) != $length()}">
                                <!-- ko if: ($index()+1) == $length() -->
                                <span
                                    data-bind="template: {name: $data.peopleSelectorVM.template, data: $data.peopleSelectorVM}"></span>
                                <!-- /ko -->
                            </td>
                            <td class="tdButton">
                                <!-- ko if: ($index()+1) == $length() -->
                                <div class="btn-find inactive"
                                     data-bind="click: $parent.navigateToNewSearchMainPage, css: {inactive: $parent.formNotFilled}"></div>
                                <!-- /ko -->
                            </td>

                        </tr>
                    </table>
                </div>


                <!-- /ko -->
            </div>
            <!-- END BOARD CONTENT -->


        </div>


        <!-- END CONSTRUCTOR -->

    </div>
    <div class="clear"></div>
    <!-- BTN MINIMIZE -->
    <a href="#" class="btn-minimizePanel"
       data-bind="click: itemsToBuy.togglePanel,html: '<span></span>'+itemsToBuy.showPanelText()"><span></span></a>
</div>
<!-- END PANEL -->
<!-- CALENDAR -->
<div class="calenderWindow z-indexTop"
     data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendarEvent}"
     style="top: -302px; overflow: hidden; height: 341px;display:none;">
</div>
<!-- END CALENDAR -->


<!-- END CENTER BLOCK -->

<!--====**********===-->
<div class="center-block" data-bind="if: itemsToBuy.correctTour()">
    <div class="allTripEvent">
        <h2>Ваша поездка во всех подробностях</h2>
        <table class="allTripTable">
            <tr>
                <td class="firstTd">
                    <div data-bind="template: {name: 'print-event-trip', data: itemsToBuy}"></div>
                    <div class="hr-bg big">
                        <img width="100%" height="31" src="/themes/v2/images/shadow-hotel.png">
                    </div>
                    <div class="shareSocial">
                    </div>
                    <div class="btn-order floatRight"
                         data-bind="click: itemsToBuy.activePanel().navigateToNewSearchMainPage, css: {inactive: itemsToBuy.activePanel().formNotFilled}">
                        Проверить<span class="l"></span></div>
                </td>
                <td class="secondTd">

                </td>
            </tr>
        </table>
    </div>
</div>
<script type="text/html" id="print-event-trip">
    <div class="allTrip" data-bind="foreach: items">
        <div class="block" data-bind="if: $index()==0">
            <div class="when">
                <div class="date" data-bind="html: $parent.dateHtml()">
                </div>
            </div>
            <div class="info">
                <div class="text">
                    <table class="headTitle">
                        <tr>
                            <td class="icoTD">
                                <div class="ico-hotel"></div>
                            </td>
                            <td class="nameTD">
                                <div class="title"><span data-bind="text:$parent.startCity"> Санкт-Петербург</span><span
                                        class="f13"> &mdash; начало путешествия</span></div>
                            </td>
                            <td class="costTD"></td>
                            <td class="removeTD"></td>
                        </tr>
                    </table>
                </div>
                <div class="hr-bg">
                    <img src="<?php echo $images . '/images/shadow-hotel.png' ?>" width="100%" height="31">
                </div>
            </div>
        </div>
        <div class="block" data-bind=" css:{end: $index()==$length()-1}">
            <div class="when">
                <div class="date" data-bind="html:dateHtml(),attr: {'class': 'date '+ dateClass()} ">
                </div>
            </div>
            <div class="info">
                <div class="text">
                    <table class="headTitle">
                        <tr>
                            <td class="icoTD">
                                <div data-bind="css: {'ico-jet': isAvia(), 'ico-hotel': isHotel()}"></div>
                            </td>
                            <td class="nameTD">
                                <div class="title" data-bind="html: overviewText()"></div>
                            </td>
                            <td class="costTD">
                                <span data-bind="text: $parent.overviewPeople()"></span>
                                <span class="costs" data-bind="html: priceHtml()"></span>
                            </td>
                        </tr>
                    </table>
                    <div data-bind="template: {name: overviewTemplate, data: $data}"></div>
                    <!-- ЗДЕСЬ БИЛЕТ -->

                </div>
                <div class="hr-bg">
                    <img src="<?php echo $images . '/images/shadow-hotel.png' ?>" width="100%" height="31">
                </div>
            </div>
        </div>
    </div>
</script>
<script id="tours-event-avia-ticket" type="text/html">
    <div class="ticket-items">
        <div class="content">
            <div class="airlines">

            </div>
            <!-- END AIRLINES -->
            <div class="center-ticket">
                <div class="date-time-city" data-bind="css: {first: roundTrip}">
                    <div class="start">
                        <div class="date" data-bind="text: departureDayMo()">
                            28 мая
                        </div>
                        <div class="time" data-bind="text: departureTime()">
                            21:20
                        </div>
                        <div class="city" data-bind="text: departureCity()">
                            Москва
                        </div>
                        <div class="airport" data-bind="text: departureAirport()">
                            Домодедово
                        </div>
                    </div>
                    <!-- END START -->
                    <div class="how-long">
                        <div class="time">
                            В пути <span data-bind="text: duration()">8 ч. 30 м.</span>
                        </div>
                        <div class="ico-path" data-bind="html: stopsRatio()">
                        </div>
                        <div class="path" data-bind="text:stopoverText(), css: {'no-wait': direct()}">
                        </div>
                    </div>
                    <!-- END HOW LONG -->
                    <div class="finish">
                        <div class="date" data-bind="text: arrivalDayMo()">
                            29 мая
                        </div>
                        <div class="time" data-bind="text: arrivalTime()">
                            00:50
                        </div>
                        <div class="city" data-bind="text: arrivalCity()">
                            Санкт-Петербург
                        </div>
                        <div class="airport" data-bind="text: arrivalAirport()">
                            Пулково
                        </div>
                    </div>
                    <!-- END FINISH -->
                    <div class="clear"></div>
                    <div class="airlinesLogo">
                        <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}">
                        <br>
                        <span data-bind="text:airlineName">Россия</span>
                    </div>
                </div>
                <!-- END DATE TIME CITY -->
                <!-- ko if: roundTrip -->
                <div class="line-two-ticket">
                    <span class="l"></span>
                    <span class="end"></span>
                    <span class="r"></span>
                </div>
                <div class="date-time-city">
                    <div class="start">
                        <div class="date" data-bind="text: rtDepartureDayMo()">
                            28 мая
                        </div>
                        <div class="time" data-bind="text: rtDepartureTime()">
                            21:20
                        </div>
                        <div class="city" data-bind="text: rtDepartureCity()">
                            Москва
                        </div>
                        <div class="airport" data-bind="text: rtDepartureAirport()">
                            Домодедово
                        </div>
                    </div>
                    <!-- END START -->
                    <div class="how-long">
                        <div class="time">
                            В пути <span data-bind="text: rtDuration()">8 ч. 30 м.</span>
                        </div>
                        <div class="ico-path" data-bind="html: rtStopsRatio()">
                        </div>
                        <div class="path" data-bind="text:rtStopoverText(), css: {'no-wait': rtDirect()}">
                        </div>
                    </div>
                    <!-- END HOW LONG -->
                    <div class="finish">
                        <div class="date" data-bind="text: rtArrivalDayMo()">
                            29 мая
                        </div>
                        <div class="time" data-bind="text: rtArrivalTime()">
                            00:50
                        </div>
                        <div class="city" data-bind="text: rtArrivalCity()">
                            Санкт-Петербург
                        </div>
                        <div class="airport" data-bind="text: rtArrivalAirport()">
                            Пулково
                        </div>
                    </div>
                    <!-- END FINISH -->
                    <div class="clear"></div>
                    <div class="airlinesLogo">
                        <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}">
                        <br>
                        <span data-bind="text:airlineName">Россия</span>
                    </div>
                </div>
                <!-- END DATE TIME CITY -->
                <!-- /ko -->

            </div>
            <div class="buy-ticket">
                <div class="text">
                    <!-- FIXME -->
                    <span class="txtBuy" data-bind="text: price"></span> <span class="rur">o</span>
                </div>
            </div>
            <!-- END BUY TICKET -->
            <div class="clear"></div>
        </div>

        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="bh"></span>
    </div>
</script>
<script id="tours-event-hotels-ticket" type="text/html">
    <div class="hotels-tickets">
        <div class="content">
            <div class="full-info">
                <div class="preview-photo">
                    <ul>
                        <li><a href="#" data-bind="click: showPhoto,attr: {'href': frontPhoto.largeUrl}"
                               class="photo"><img data-bind="attr:{src: frontPhoto.largeUrl}"></a></li>
                    </ul>
                    <div class="how-much" data-bind="visible: numPhotos">
                        <a href="#">Фотографий (<span data-bind="text: numPhotos">11</span>)</a>
                    </div>
                </div>
                <div class="description">
                    <div class="title">
                        <h2><span data-bind="text:hotelName">Рэдиссон Соня Отель</span> <span class="gradient"></span>
                        </h2>

                        <div data-bind="attr: {'class': 'stars ' + stars}"></div>
                    </div>
                    <div class="place">
                        <div class="street">
                            <span data-bind="text:address">Санкт-Петребург. ул. Морская Набережная, 31/2</span>
                            <span class="gradient"></span>
                        </div>
                        <a href="#" data-bind="click: showMapDetails" class="in-the-map"><span
                                class="ico-see-map"></span> <span class="link">На карте</span></a>
                    </div>
                    <div class="text">
                        <span data-bind="html: limitDesc.startText">Этот 4-звездочный отель расположен рядом с площадью Победы и парком Городов-Героев. К услугам гостей большой крытый бассейн и номера с телевизорами с плоским экраном...</span><span
                            data-bind="visible: limitDesc.isBigText">...</span>
                    </div>
                </div>
                <div class="choose-a-hotel">
                    <div class="rating" data-bind="visible: rating">
                        <div class="textRating" onmouseover="ratingHoverActive(this)"
                             onmouseout="ratingHoverNoActive(this)">
                            <span class="value" data-bind="text: rating"></span>
                            <span class="text" data-bind="html: ratingName">рейтинг<br>отеля</span>
                        </div>
                        <div class="descrRating">
                            <strong><span data-bind="text: rating"></span> из 5 баллов</strong>
                            Рейтинг построен на основе анализа данных о качестве отеля и отзывах его посетителей.
                        </div>
                    </div>
                    <a class="details" data-bind="click: showDetails" href="#">Подробнее об отеле</a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="details">
                <ul>
                    <li class="not-show" data-bind="template: {name: 'hotel-roomSet-template', data: roomSets()[0]}"/>
                </ul>
                <!-- div class="tab-ul" data-bind="visible: visibleRoomSets().length > 2">
                   <a href="#" data-bind="click: showAllResults,text: showAllText(),attr: {'class': isShowAll() ? 'active' : ''}">Посмотреть все результаты</a>
               </div -->
                <span class="lv"></span>
                <span class="rv"></span>
            </div>
        </div>
        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="bh"></span>
    </div>

</script>