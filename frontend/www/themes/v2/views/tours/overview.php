<?php
$images = 'themes/v2';
?>
<script id="tours-index"></script>
<script id="tours-overview" type="text/html">
  <div class="main-block">
    <div id="content">
      <div class="headAndTmblr">
	<h1>Ваша поездка, сэр</h1>
	<ul class="tmblr">
	  <li class="active"><span class="ico-descr"></span> <a href="#descr">Таймлайн</a></li>
	  <li><span class="ico-see-map"></span> <a href="#map">На карте</a></li>
	</ul>
      </div>
   
   <table class="allTripTable">   
      <tr>
      <td class="firstTd">
      <div class="allTrip" data-bind="foreach: data">
	<div class="block" data-bind="template: {if: $index()==0, name:'tours-overview-start', data:$parent}">
	</div>
	<div class="block" data-bind="template: {name:'tours-overview-entry', data:$data}, css:{end: $index()==$length()-1}">
	</div>    
      </div>
      </td>
      <td class="secondTd">
      </td>
      </tr>
   </table>
      <div class="hr-bg">
        <img src="<?php echo $images.'/images/shadow-hotel.png' ?>" width="100%" height="31">
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
	  <td class="icoTD">
            <div class="ico-hotel"></div>      </td>
      <td class="nameTD">
            <div class="title"><span  data-bind="text:vm.startCity()">Санкт-Петербург</span><span class="f13"> &mdash; начало путешествия</span></div>
	  </td>
	  <td class="costTD"></td>
      <td class="removeTD"></td>
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
	  <td class="icoTD">
            <div data-bind="css: {'ico-jet': isAvia(), 'ico-hotel': isHotel()}"></div>
      </td>
      <td class="nameTD">
            <div class="title" data-bind="html: overviewText()"></div>
	  </td>
	  <td class="costTD">
            <span data-bind="text: overviewPeople()"></span>
            <span class="costs" data-bind="html:priceHtml()"></span>
      </td>
      <td class="removeTD">      
            <a href="#" class="btnDeleteTrip" data-bind="click: $parents[1].removeItem"></a>
	  </td>
	</tr>
      </table>
      <div data-bind="template: {name: overviewTemplate, data: selection()}"></div>
      <!-- ЗДЕСЬ БИЛЕТ -->
      <table class="descrTicket">
	<tr data-bind="if: isAvia()">
	  <td class="text"><span data-bind="text:selection()?'Или другой':'Выберите'"></span> вариант среди: <span class="f19" data-bind="text:numAirlines()">19</span> авиакомпаний, от <span class="f19"  data-bind="html: minPriceHtml()"></span> до <span class="f19"  data-bind="html: maxPriceHtml()"></span></td>
	  <td class="buttons">
            <a class="btn-cost" href="#" data-bind="click: $parents[1].setActive">
              <span class="l"></span>
              <span class="text">Все авиабилеты</span>
            </a>
	  </td>
	</tr>
	<tr data-bind="if: isHotel()">
	  <td class="text"><span data-bind="text:selection()?'Или другой':'Выберите'"></span> вариант среди: <span class="f19" data-bind="text:numHotels()">189</span> гостиниц, от <span class="f19" data-bind="html: minPriceHtml()"></span> до <span class="f19" data-bind="html: maxPriceHtml()"></span></td>
	  <td class="buttons">
            <a class="btn-cost hotel" href="#" data-bind="click: $parents[1].setActive">
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
<script id="tours-overview-hotels-ticket" type="text/html">
  <div class="hotels-tickets">
    <div class="content">
      <div class="full-info" data-bind="with:hotel">
        <div class="preview-photo">
          <ul>
            <li><a href="#" data-bind="click: showPhoto,attr: {'href': frontPhoto.largeUrl}" class="photo"><img data-bind="attr:{src: frontPhoto.largeUrl}"></a></li>
          </ul>
          <div class="how-much" data-bind="visible: numPhotos">
            <a href="#">Фотографий (<span data-bind="text: numPhotos">11</span>)</a>
          </div>
        </div>
        <div class="description">
          <div class="title">
            <h2><span data-bind="text:hotelName">Рэдиссон Соня Отель</span> <span class="gradient"></span></h2>
            <div data-bind="attr: {class: 'stars ' + stars}"></div>
          </div>
          <div class="place">
            <div class="street">
              <span data-bind="text:address">Санкт-Петребург. ул. Морская Набережная, 31/2</span>
              <span class="gradient"></span>
            </div>
            <a href="#"  data-bind="click:showMapDetails" class="in-the-map"><span class="ico-see-map"></span> <span class="link">На карте</span></a>
          </div>
          <div class="text" data-bind="text:description">
            Этот 4-звездочный отель расположен рядом с площадью Победы и парком Городов-Героев. К услугам гостей большой крытый бассейн и номера с телевизорами с плоским экраном...
          </div>
        </div>
        <div class="choose-a-hotel">
          <div class="rating"  data-bind="visible: rating!='-'">
            <span class="value" data-bind="text: rating"></span>
            <span class="text">рейтинг<br>отеля</span>
          </div>
        </div>
        <div class="clear"></div>
      </div>
      <div class="details">
        <ul>
          <li class="not-show">
            <div class="items">
              <div class="float" data-bind="foreach: roomSet.rooms">
                <span class="text"><span data-bind="text: name">Стандартный двухместный номер</span> <span data-bind="text: nameNemo"></span></span>
                <!-- ko if: hasMeal -->
                <span class="ico-breakfast"></span> <span data-bind="text:meal">Завтрак</span>
                <!-- /ko -->
                <br>
              </div>
              <div class="how-cost">
                <span class="cost" data-bind="text: roomSet.pricePerNight">14 200</span><span class="rur f21">o</span> / ночь <br> <span class="grey em" data-bind="visible: roomSet.rooms.length == 2">За оба номера</span>
              </div>
              <div class="clear"></div>
            </div>
          </li>
        </ul>
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
<script id="tours-overview-avia-no-selection" type="text/html">
</script>
<script id="tours-overview-hotels-no-selection" type="text/html">
</script>
