<script id="avia-cheapest-result" type="text/html">
  <div class="recommendedBlock">
    <div class="recommended-ticket" data-bind="template:{name: 'recommend-ticket-template', data:{data:cheapest(), ribbon:'ribbon-cheapest'}}">
    </div>
    <div class="prices-of-3days" data-bind="template:{name: 'prices-of-3days-template', data:$data}">
    </div>
    <div class="clear"></div>
  </div>
  <div class="backgroundLineRec"> </div>
</script>
<script id="avia-tours-recommend" type="text/html">
  <div class="recommended-ticket" data-bind="template:{name: 'recommend-ticket-template', data:{data:cheapest(), ribbon:'ribbon-cheapest'}}">
  </div>
  <div class="recommended-ticket" data-bind="template:{name: 'recommend-ticket-template', data:{data:best(), ribbon:'ribbon-optima'}}">
  </div>
  <div class="clear"></div>
  <div class="backgroundLineRec"> </div>
</script>

<script id="prices-of-3days-template" type="text/html">
	<h3>Цены ±3 дня</h3>
    <div class="ticket" data-bind="with:siblings">
    	
      <div class="one-way">
        <ul class="schedule-of-prices" data-bind="foreach: $data">
          <li data-bind="css: {active: isActive}, click: $parent.select">
            <div class="price" data-bind="style: {bottom: ($parent.graphHeight()-scaledHeight()+45) + 'px'}, text: columnValue(), visible: !nodata">-100</div>
            <div class="chart" data-bind="style: {backgroundPosition: background()}" ></div>
	    <div class="price" data-bind="visible:nodata" style="bottom: 40px">?</div>
            <div class="week" data-bind="text: dow">пн</div>
            <div class="date" data-bind="text: date">16</div>
            <div class="month" data-bind="visible: showMonth, text: month">
              Май
            </div>
          </li>
        </ul>
      </div>
      <div class="two-way" data-bind="visible: roundTrip">
        <ul class="schedule-of-prices" data-bind="foreach: active">
          <li data-bind="css: {active: isActive}, click: $parent.select">
            <div class="price" data-bind="style: {bottom: ($parent.graphHeight()-scaledHeight()+45) + 'px'}, text: columnValue(), visible: !nodata">-100</div>
            <div class="chart" data-bind="style: {backgroundPosition: background()}" ></div>
	    <div class="price" data-bind="visible:nodata" style="bottom: 40px">?</div>
            <div class="week" data-bind="text: dow">пн</div>
            <div class="date" data-bind="text: date">16</div>
            <div class="month" data-bind="visible: showMonth, text: month">
              Май
            </div>
          </li>
        </ul>
      </div>
      
      <div class="blockText">
        <div class="txt" data-bind="visible: !selection().price">Данные получены на основании поисковых запросов и могут отличаться от актуальных значений</div>
        <div class="txtCena" data-bind="visible: selection().price">
	  <div class="leftFloat">
	    Итого <span class="price" data-bind="text: selection().price">4150</span> <span class="rur">o</span>
	  </div>
	  <div class="rightFloat">
	    <a class="btnLook" href="#" data-bind="click: search">Посмотреть</a>
	  </div>
	  <div class="clear"></div>
        </div>
      </div>
      <span class="lt"></span>
      <span class="rt"></span>
      <span class="lv"></span>
      <span class="rv"></span>
      <span class="th"></span>
      <span class="bh"></span>
    </div>

</script>
<script id="recommend-ticket-template" type="text/html">
  <div class="ticket-items">
    <div data-bind="attr:{class: ribbon}"></div>
    <div class="content">
      <div class="airlines-line">
        <img data-bind="attr: {'src': '/img/airline_logos/' + data.airline +'.png'}" >
        <span data-bind="text:data.airlineName">Россия</span>
      </div>
      <div class="date-time-city">
        <div class="start">
          <div class="date" data-bind="text: data.departureDayMo()">
            28 мая
          </div>
          <div class="time" data-bind="text: data.departureTime()">
            21:20
          </div>
          <div class="city" data-bind="text: data.departureCity()">Москва</div>
          <div class="airport" data-bind="text: data.departureAirport()">
            Домодедово
          </div>
        </div>
        <div class="how-long">
          <div class="path">
            В пути
          </div>
          <div class="ico-path" data-bind="html: data.recommendStopoverIco()"></div>
          <div class="time" data-bind="text: data.duration()">
            3 ч. 30 м.
          </div>
        </div>
        <div class="finish">
          <div class="date" data-bind="text: data.arrivalDayMo()">
            29 мая
          </div>
          <div class="time" data-bind="text: data.arrivalTime()">
            00:50
          </div>
          <div class="city" data-bind="text: data.arrivalCity()">Санкт-Петербург</div>
          <div class="airport" data-bind="text: data.arrivalAirport()">
            Пулково
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <!-- END DATE -->
      <!-- ko if: data.stacked() -->
      <div class="other-time">
	<div class="variation">
	  <ul class="minimize">
	    <li>
	      Варианты вылета:
	    </li>
	    <!-- ko foreach: data.voyages -->
	    <li data-bind="css: {active: hash()==$parent.data.hash()}, click: $parent.data.chooseStacked, visible: visible">
	      <input name="cheapest_stacked" type="radio"  data-bind="value: hash(), checked: $parent.data.hash()">
	      <label><span data-bind="text:departureTime()">06:10</span></label>
	    </li>
	    <!-- /ko -->
	  </ul>
	</div>
	<a href="#" class="left" data-bind="css: {none: data.hash() == data.voyages[0].hash()}, click: data.choosePrevStacked"></a>
	<a href="#" class="right" data-bind="css: {none: data.hash() == data.voyages[data.voyages.length-1].hash()}, click: data.chooseNextStacked"></a>
      </div>
      <!-- /ko -->
	
      <!-- ko if: data.roundTrip -->
      <div class="line-two-ticket">
        <span class="end"></span>
      </div>
      <div class="airlines-line">
        <img data-bind="attr: {'src': '/img/airline_logos/' + data.airline +'.png'}" >
        <span data-bind="text:data.airlineName">Россия</span>
      </div>
      <div class="date-time-city">
        <div class="start">
          <div class="date" data-bind="text: data.rtDepartureDayMo()">
            28 мая
          </div>
          <div class="time" data-bind="text: data.rtDepartureTime()">
            21:20
          </div>
          <div class="city" data-bind="text: data.rtDepartureCity()">
            Москва
          </div>
          <div class="airport" data-bind="text: data.rtDepartureAirport()">
            Домодедово
          </div>
        </div>
        <div class="how-long">
          <div class="path">
            В пути
          </div>
          <div class="ico-path" data-bind="html: data.rtRecommendStopoverIco()"></div>
          <div class="time" data-bind="text: data.rtDuration()">
            3 ч. 30 м.
          </div>
        </div>
        <div class="finish">
          <div class="date" data-bind="text: data.rtArrivalDayMo()">
            29 мая
          </div>
          <div class="time" data-bind="text: data.rtArrivalTime()">
            00:50
          </div>
          <div class="city" data-bind="text: data.rtArrivalCity()">
            Санкт-Петербург
          </div>
          <div class="airport" data-bind="text: data.rtArrivalAirport()">
            Пулково
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <!-- /ko -->
      <!-- END DATE -->
	
      <!-- ko if: data.rtStacked() -->
      <div class="other-time">
	<div class="variation">
	  <ul class="minimize">
	    <li>
	      Варианты вылета:
	    </li>
	    <!-- ko foreach: data.rtVoyages() -->
	    <li data-bind="css: {active: hash()==$parent.data.rtHash()}, click: $parent.data.chooseRtStacked, visible: visible">
	      <input name="cheapest_rt_stacked" type="radio"  data-bind="value: hash(), checked: $parent.data.rtHash()">
	      <label><span data-bind="text:departureTime()">06:10</span></label>
	    </li>
	    <!-- /ko -->
	  </ul>
	</div>
	<a href="#" class="left" data-bind="css: {none: data.rtHash() == data.rtVoyages()[0].hash()}, click: data.choosePrevRtStacked"></a>
	<a href="#" class="right" data-bind="css: {none: data.rtHash() == data.rtVoyages()[data.rtVoyages().length-1].hash()}, click: data.chooseNextRtStacked"></a>
      </div>
      
      <!-- /ko -->
      
      <div class="line-dashed-ticket">
        <span class="end"></span>
      </div>
      <div class="details-selecte">
        <div class="details">
          <a data-bind="click: data.showDetails" href="#">Подробнее<br> о перелете</a>
        </div>
        <a href="#" class="btn-cost" data-bind="click: $parent.select, css:{selected:$parent.selected_key()==data.key}">
          <span class="l"></span>
          <span class="text" data-bind="text: $parent.tours?($parent.selected_key()==data.key?'Выбран':'Выбрать'):'Купить'"></span>
          <span class="price" data-bind="text: data.price"></span>
          <span class="rur">o</span>
        </a>
      </div>
    </div>
    
    <span class="lt"></span>
    <span class="rt"></span>
    <span class="lv"></span>
    <span class="rv"></span>
    <span class="bh"></span>
  </div>
</script>
