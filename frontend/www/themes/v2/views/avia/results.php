<script type="text/html" id="avia-results">
<h1><span>Выберите авиабилет</span>
    <span data-bind="text: results.departureCity">Санкт-Петербург</span> → <span data-bind="text: results.arrivalCity">Амстердам</span>, <span data-bind="text: results.date">19 мая</span></h1>

<div class="recomended-content" data-bind="template: {name: 'avia-cheapest-result', data: results.cheapest}">

</div>
<!-- END RECOMENDED AND GRAFIK -->
<div class="clear"></div>
</div>
<div class="ticket-content">
<h2>Все результаты. Найдено: <span data-bind="text: results.numResults()"></span>.</h2>
<div class="order-div"><a class="order-hide" href="#" style="display: inline;">Скрыть рекомендации</a></div>
<div class="clear"></div>

<!-- ko foreach: results -->
<div class="ticket-items" data-bind="visible: visible()">
<div class="content">
<div class="airlines">

</div>
<!-- END AIRLINES -->
<div class="center-ticket">
<div class="date-time-city">
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
        <div class="path" data-bind="text:stopoverText()">
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
        <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}" >
        <br>
        <span data-bind="text:airlineName">Россия</span>
    </div>
</div>
<!-- END DATE TIME CITY -->
<!-- ko if: stacked() -->
<div class="other-time">
    <div class="title">Также вы можете вылететь в</div>
    <div class="btn-minimize" data-bind="css:{up: !stackedMinimized()}"><a href="#" data-bind="click: minimizeStacked, text: stackedMinimized()?'Списком':'Свернуть'">Списком</a></div>
    <div class="clear"></div>
    <ul data-bind="foreach: voyages, css:{expand: !stackedMinimized(), minimize:stackedMinimized()}">
        <li data-bind="visible: visible()">
            <a href="#" class="ico-path-time" data-bind="css: {hover: hash() == $parent.hash() }, click: $parent.chooseStacked">
                <input type="radio" data-bind="value: hash(), checked: $parent.hash()">

                <div class="path">
                    <div class="in-path"><span>В пути </span><span data-bind="text: duration()">9 ч. 20 м.</span></div>
                    <div class="start" data-bind="text:departureTime()">06:10</div>
                    <div class="finish" data-bind="text:arrivalTime()">08:10</div>
                </div>
            </a>
        </li>
    </ul>
</div>
<!-- /ko -->
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
        <div class="ico-path" data-bind="html: stopsRatio()">
        </div>
        <div class="path" data-bind="text:rtStopoverText()">
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
        <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}" >
        <br>
        <span data-bind="text:airlineName">Россия</span>
    </div>

</div>
<!-- END DATE TIME CITY -->
<!-- ko if:rtStacked() -->
<div class="other-time" >
  <div class="title">Также вы можете вылететь в</div>
  <div class="btn-minimize" data-bind="css:{up: !rtStackedMinimized()}"><a href="#" data-bind="click: minimizeRtStacked, text: rtStackedMinimized()?'Списком':'Свернуть'">Списком</a></div>
  <div class="clear"></div>
  <ul class="minimize" data-bind="foreach: rtVoyages(), css:{expand: !rtStackedMinimized(), minimize:rtStackedMinimized()}">
    <li data-bind="visible: visible()">
      <a href="#" class="ico-path-time" data-bind="css: {hover: hash() == $parent.rtHash() }, click: $parent.chooseRtStacked">
        <input type="radio" data-bind="value: hash(), checked: $parent.rtHash()">
	
        <div class="path">
          <div class="in-path"><span>В пути </span><span data-bind="text: duration()">9 ч. 20 м.</span></div>
          <div class="start" data-bind="text:departureTime()">06:10</div>
          <div class="finish" data-bind="text:arrivalTime()">08:10</div>
        </div>
      </a>
    </li>
  </ul>
</div>
<!-- /ko -->
<!-- /ko -->

</div>
<!-- END CENTER BLOCK -->
<div class="buy-ticket">
    <div class="text">
        <span>Купить</span>
        <a href="#" class="btn-cost">
            <span class="l"></span>
            <span class="price" data-bind="text: price">3 250</span>
            <span class="rur">o</span>
        </a>
    </div>
    <a href="#" data-bind="click: showDetails" class="details">Подробнее <span>о перелете</span></a>
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
<!-- END TICKET -->
<!-- /ko -->
</div>
<!-- FIXME: avia-hotel duplication -->
<div id="avia-body-popup" class="body-popup" style="display:none;" >
  <div id="avia-popup-body" class="popup">
    <div>
      <div id="boxTopLeft"></div>
      <div id="boxTopCenter"></div>
      <div id="boxTopRight"></div>
      <div class="clear"></div>
    </div>
    <div>
      <div id="boxMiddleLeft"></div>
      <div id="boxContent">
        <div id="contentBox">
          <div data-bind="template: {name: 'avia-popup', data: results.popup()}"></div>
          <div id="boxClose" data-bind="click: results.popup().closeDetails"></div>
        </div>
      </div>
      <div id="boxMiddleRight"></div>
      <div class="clear"></div>
    </div>
    <div>
      <div id="boxBottomLeft"></div>
      <div id="boxBottomCenter"></div>
      <div id="boxBottomRight"></div>
    </div>
  </div>
</div>
</script>
