<script id="avia-cheapest-result" type="text/html">
  <div class="recommended-ticket" data-bind="template:{name: 'recommend-ticket-template', data:{data:cheapest(), ribbon:'ribbon-cheapest'}}">
  </div>
  <div class="prices-of-3days" data-bind="template:{name: 'prices-of-3days-template', data:$data}">
  </div>
  <div class="clear"></div>
</script>
<script id="avia-tours-recommend" type="text/html">
  <div class="recommended-ticket" data-bind="template:{name: 'recommend-ticket-template', data:{data:cheapest(), ribbon:'ribbon-cheapest'}}">
  </div>
  <div class="recommended-ticket" data-bind="template:{name: 'recommend-ticket-template', data:{data:best(), ribbon:'ribbon-optima'}}">
  </div>
  <div class="clear"></div>
</script>

<script id="prices-of-3days-template" type="text/html">
  <div class="prices-of-3days">
    <div class="ticket">
      <div class="one-way">
        <ul class="schedule-of-prices">
          <li>
            <div class="price" style="bottom: 80px">-100</div>
            <div class="chart" style="background-position: center 55px;"></div>
            <div class="week">пн</div>
            <div class="date">16</div>
          </li>
          <li>
            <div class="price" style="bottom: 55px">-100</div>
            <div class="chart" style="background-position: center 80px;"></div>
            <div class="week">вт</div>
            <div class="date">17</div>
          </li>
          <li>
            <div class="price" style="bottom: 75px">-100</div>
            <div class="chart" style="background-position: center 60px;"></div>
            <div class="week">ср</div>
            <div class="date">18</div>
          </li>
          <li class="active">
            <div class="price" style="bottom: 85px">3 250</div>
            <div class="chart" style="background-position: center 50px;"></div>
            <div class="week">чт</div>
            <div class="date">19</div>
          </li>
          <li>
            <div class="price" style="bottom: 75px">-100</div>
            <div class="chart" style="background-position: center 60px;"></div>
            <div class="week">пт</div>
            <div class="date">20</div>
          </li>
          <li>
            <div class="price" style="bottom: 110px">-100</div>
            <div class="chart" style="background-position: center 25px;"></div>
            <div class="week">сб</div>
            <div class="date">21</div>
          </li>
          <li>
            <div class="price" style="top: 45px">-100</div>
            <div class="chart" style="background-position: center 60px;"></div>
            <div class="week">вс</div>
            <div class="date">22</div>
          </li>
        </ul>
        <div class="month">
          Май
        </div>
      </div>
      <div class="two-way" data-bind="visible: roundTrip">
        <ul class="schedule-of-prices">
          <li>
            <div class="price" style="bottom: 80px">-100</div>
            <div class="chart" style="background-position: center 55px;"></div>
            <div class="week">пн</div>
            <div class="date">16</div>
          </li>
          <li>
            <div class="price" style="bottom: 55px">-100</div>
            <div class="chart" style="background-position: center 80px;"></div>
            <div class="week">вт</div>
            <div class="date">17</div>
          </li>
          <li>
            <div class="price" style="bottom: 75px">-100</div>
            <div class="chart" style="background-position: center 60px;"></div>
            <div class="week">ср</div>
            <div class="date">18</div>
          </li>
          <li class="active">
            <div class="price" style="bottom: 85px">3 250</div>
            <div class="chart" style="background-position: center 50px;"></div>
            <div class="week">чт</div>
            <div class="date">19</div>
          </li>
          <li>
            <div class="price" style="bottom: 75px">-100</div>
            <div class="chart" style="background-position: center 60px;"></div>
            <div class="week">пт</div>
            <div class="date">20</div>
          </li>
          <li>
            <div class="price" style="bottom: 110px">-100</div>
            <div class="chart" style="background-position: center 25px;"></div>
            <div class="week">сб</div>
            <div class="date">21</div>
          </li>
          <li>
            <div class="price" style="top: 45px">-100</div>
            <div class="chart" style="background-position: center 60px;"></div>
            <div class="week">вс</div>
            <div class="date">22</div>
          </li>
        </ul>
        <div class="month">
          Май
        </div>
      </div>
      
      <div class="blockText">
        <div class="txt">Данные получены на основании поисковых запросов и могут отличаться от актуальных значений</div>
        <div class="txtCena" style="display:none">
	  <div class="leftFloat">
	    Итого <span class="price">4150</span> <span class="rur">o</span>
	  </div>
	  <div class="rightFloat">
	    <a class="btnLook" href="#">Посмотреть</a>
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
