<?php
    $images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
?>
<script id="tours-overview">
  <div class="main-block">
    <div id="content">
      <div class="headAndTmblr">
	<h1>Ваша поездка, сэр</h1>
	<ul class="tmblr">
	  <li class="active"><span class="ico-descr"></span> <a href="#descr">Таймлайн</a></li>
	  <li><span class="ico-see-map"></span> <a href="#map">На карте</a></li>
	</ul>
      </div>
      <div class="allTrip" data-bind="foreach: data">
	<div class="block" data-bind="template: {if: $index()==0, name:'tours-overview-start', data:$parent}">
	</div>
	<div class="block" data-bind="template: {name:'tours-overview-entry', data:$data}">
	</div>
    
	<div class="block">
	</div>
    
	<div class="block end">
	  <div class="when">
            <div class="date two">
              <div class="day">
		<span class="f17">12</span>
		<br>
		мая
              </div>
              <div class="day">
		<span class="f17">12</span>
		<br>
		мая
              </div>
            </div>
	  </div>
	  <div class="info">
            <div class="text">
              <table class="headTitle">
		<tr>
		  <td class="name">
                    <div class="ico-hotel"></div>
                    <div class="title">Отель в Амстердам</div>
		  </td>
		  <td class="allCost">
                    2 человека, 7 дней:
                    <span class="costs">58 000 <span class="rur">o</span></span>
                    <a href="#" class="btnDeleteTrip"></a>
		  </td>
		</tr>
              </table>
              <!-- ЗДЕСЬ БИЛЕТ -->
              <table class="descrTicket">
		<tr>
		  <td class="text">Вы можете подобрать другой вариант среди: <span class="f19">189</span> гостиниц, от <span class="f19">3 500 <span class="f19 rur">o</span></span> до <span class="f19">14 770 <span class="f19 rur">o</span></span></td>
		  <td class="buttons">
                    <a class="btn-cost hotel" href="#">
                      <span class="l"></span>
                      <span class="text">Все отели</span>
                    </a>
		  </td>
		</tr>
              </table>
            </div>
            <div class="hr-bg">
              <img src="<?php echo $images.'/images/bg-hr-trip-all.png' ?>" width="100%" height="31">
            </div>
	  </div>
	</div>
      </div>
      <div class="hr-bg">
        <img src="<?php echo $images.'/images/bg-hr-trip-all.png' ?>" width="100%" height="31">
      </div>
      <h3 class="calendarTitle">Календарь поездки</h3>
      <!-- CALENDAR -->
      <br>
      <div class="hr-bg">
        <img src="<?php echo $images.'/images/bg-hr-trip-all.png' ?>" width="100%" height="31">
      </div>
      <div class="costItAll">
	Итого <span class="allCost">86 250 <span class="rur">o</span></span>
	<a href="#" class="btnGoBuy"></a><br>

	<a href="#" class="detailCost"><img src="<?php echo $images.'/images/detail-cost.png'?>"></a>
      </div>
    </div>
    <div class="clear"></div>
  </div>
</script>
<script id="tours-overview-start" type="text/html">
  <div class="when">
    <div class="date" data-bind="html: vm.dateHtml()">
    </div>
  </div>
  <div class="info">
    <div class="text">
      <table class="headTitle">
	<tr>
	  <td class="name">
            <div class="ico-hotel"></div>
            <div class="title"><span  data-bind="text:vm.startCity()">Санкт-Петербург</span> &mdash; <span class="f13">начало путешествия</span></div>
	  </td>
	  <td class="allCost">
	  </td>
	</tr>
      </table>
    </div>
    <div class="hr-bg">
      <img src="<?php echo $images.'/images/bg-hr-trip-all.png' ?>" width="100%" height="31">
    </div>
  </div>
</script>
<script id="tours-overview-entry" type="text/html">
  <div class="when">
    <div class="date" data-bind="attr: {class: 'date '+ dateClass()}, html:dateHtml()">
    </div>
  </div>
  <div class="info">
    <div class="text">
      <table class="headTitle">
	<tr>
	  <td class="name">
            <div class="ico-jet"></div>
            <div class="title" data-bind="html: overviewText()"></div>
	  </td>
	  <td class="allCost">
            2 человека
            <span class="costs" data-bind="html:priceText()"></span>
            <a href="#" class="btnDeleteTrip"></a>
	  </td>
	</tr>
      </table>
      <div data-bind="template: {name: overviewTemplate, data: selection()}"></div>
      <!-- ЗДЕСЬ БИЛЕТ -->
      <table class="descrTicket">
	<tr>
	  <td class="text">Или другой вариант среди: <span class="f19">19</span> авиакомпаний, от <span class="f19">3 500 <span class="f19 rur">o</span></span> до <span class="f19">14 770 <span class="f19 rur">o</span></span></td>
	  <td class="buttons">
            <a class="btn-cost" href="#">
              <span class="l"></span>
                      <span class="text">Все авиабилеты</span>
            </a>
	  </td>
	</tr>
      </table>
    </div>
    <div class="hr-bg">
      <img src="<?php echo $images.'/images/bg-hr-trip-all.png' ?>" width="100%" height="31">
    </div>
  </div>
</script>
<script id="tours-overview-avia-ticket" type="text/html">
  <div class="ticket-items">
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
            <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}" >
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
	    <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}" >
	    <br>
	    <span data-bind="text:airlineName">Россия</span>
	  </div>
	</div>
	<!-- END DATE TIME CITY -->
	<!-- /ko -->

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
<script id="tours-overview-hotels-ticket" type="text/html">
123
</script>
