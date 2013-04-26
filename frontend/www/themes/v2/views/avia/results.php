<script id="avia-results" type="text/html">
  <!-- MAIN BLOCK -->
  <div class="main-block">
    <div id="content" data-bind="template: {name: results().noresults?'avia-no-results':'avia-results-inner', data: results()}">
    </div>
    <!-- END MAIN BLOCK -->
    <!-- FILTER BLOCK -->
    <div class="filter-block" data-bind="template: {name: 'avia-filters', data: results().filters, afterRender: results().filtersRendered}">
    </div>
    <!-- END FILTER BLOCK -->
    <div class="clear"></div>
  </div>
  <!-- END ALL CONTENT -->
</script>
<script type="text-html" id="avia-no-results">
<h1>В выбранные даты, к сожалению, перелета не найдено!</h1>
</script>
<script type="text/html" id="avia-results-inner">
  <h1 data-bind="visible: tours"><div class="hideTitle">Выберите авиабилет</div>
    <span data-bind="text: departureCity">Санкт-Петербург</span> → <span data-bind="text: arrivalCity">Амстердам</span>, <div class="inlineBlock"><span data-bind="text: dateHeadingText">19 мая</span></div></h1>
  
  <div class="recomended-content" data-bind="template: {name: recommendTemplate, data: $data}">
  </div>
  <!-- END RECOMENDED AND GRAFIK -->
  <div class="clear"></div>
<div class="ticket-content">
  <h2>Все результаты поиска: <span data-bind="text: numResults()"></span> авиабилетов</h2>
  <div class="order-div"><a class="order-hide" href="#" data-bind="click: hideRecommend">Скрыть рекомендации</a></div>
  <div class="clear"></div>
  
  <!-- ko foreach: data -->
  <div class="ticket-items" data-bind="visible: visible()">
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
    <div class="how-long" data-bind='click: showDetails'>
      <div class="time">
        В пути <span data-bind="text: duration()">8 ч. 30 м.</span>
        </div>
      <div class="ico-path" data-bind="html: stopsRatio()">
      </div>
      <div class="path tooltip" data-bind="text:stopoverText(), css: {'no-wait': direct()}, attr:{'rel': stopoverRelText()}">
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
        <img data-bind="attr: {'src': '/img/airline_logos/' + firstAirline() +'.png'}" >
        <br>
        <span data-bind="text:firstAirlineName()">Россия</span>
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
    <div class="how-long" data-bind='click: showDetails'>
      <div class="time">
        В пути <span data-bind="text: rtDuration()">8 ч. 30 м.</span>
      </div>
      <div class="ico-path" data-bind="html: rtStopsRatio()">
      </div>
      <div class="path tooltip" data-bind="text:rtStopoverText(), css: {'no-wait': rtDirect()}, attr:{'rel': rtStopoverRelText()}">
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
    <div class="airlinesLogo" style="margin-top: -40px;">
      <img data-bind="attr: {'src': '/img/airline_logos/' + rtFirstAirline() +'.png'}" >
      <br>
      <span data-bind="text:rtFirstAirlineName()">Россия</span>
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
      <!-- FIXME -->
      <span class="txtBuy" data-bind="text: $parent.tours?($parent.selected_key()==key?'Выбран':'Выбрать'):'Купить'"></span> 
      <a href="#" class="pressButton" data-bind="click:$parent.select, css:{selected:$parent.selected_key()==key}">
        <span class="l"></span>
        <span class="price" data-bind="text: Utils.formatPrice(price)">3 250</span>
        <span class="rur">o</span>
      </a>
    </div>
    <a href="#" data-bind="click: showDetails" class="details">Подробнее <span>о перелете</span></a> <!-- span data-bind="text:searchService"></span  -->
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
</script>
<!-- FIXME: avia-hotel duplication -->
<script id="avia-body-popup-template" type="text/html">
<div id="avia-body-popup" class="body-popup">
	<div id="layer">  
		<div class="pv_cont">
			<table cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td>
							<div id="pv_box">
						          <div data-bind="template: {name: 'avia-popup', data: data}"></div>
						          <div id="boxClose" data-bind="click: close"></div>
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
