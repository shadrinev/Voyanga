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
	  <li class="active"><span class="ico-descr"></span> <a href="javascript:void(0)">Таймлайн</a></li>
	  <li><span class="ico-see-map"></span> <a href="javascript:void(0)">На карте</a></li>
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
      <div class="hr-bg first">
        <img src="<?php echo $images.'/images/shadow-hotel.png' ?>" width="100%" height="31">
      </div>
      <h3 class="calendarTitle">Календарь поездки</h3>
      <!-- CALENDAR -->
      <br>
      <div class="hr-bg">
        <img src="<?php echo $images.'/images/shadow-hotel.png' ?>" width="100%" height="31">
      </div>
      <div class="costItAll" data-bind="visible: someSegmentsSelected">
    	Итого <span class="allCost"><span data-bind="text: price()">86 250</span> <span class="rur">o</span></span>
	    <a href="#" class="btnGoBuy"></a><br>
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
      <img src="<?php echo $images.'/images/shadow-hotel.png' ?>" width="100%" height="31">
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
            <a href="#" class="btnDeleteTrip" data-bind="click: $parents[1].removeItem" onmouseover="deletePopUp(this)" onmouseout="deletePopUpHide()" ></a>
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
      <img src="<?php echo $images.'/images/shadow-hotel.png' ?>" width="100%" height="31">
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
                    <a href="#"  data-bind="click: showMapDetails" class="in-the-map"><span class="ico-see-map"></span> <span class="link">На карте</span></a>
                </div>
                <div class="text">
                    <span data-bind="html: limitDesc.startText">Этот 4-звездочный отель расположен рядом с площадью Победы и парком Городов-Героев. К услугам гостей большой крытый бассейн и номера с телевизорами с плоским экраном...</span><span data-bind="visible: limitDesc.isBigText">...</span>
                </div>
            </div>
            <div class="choose-a-hotel">
                <div class="rating"  data-bind="visible: rating">
                    <div class="textRating" onmouseover="ratingHoverActive(this)" onmouseout="ratingHoverNoActive(this)">
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
                <li  class="not-show" data-bind="template: {name: 'hotel-roomSet-template', data: roomSet}" />
            </ul>
            <!-- div class="tab-ul" data-bind="visible: visibleRoomSets().length > 2">
                <a href="#" data-bind="click: showAllResults,text: showAllText(),attr:{class: isShowAll() ? 'active' : ''}">Посмотреть все результаты</a>
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
<script id="tours-overview-avia-no-selection" type="text/html">
</script>
<script id="tours-overview-hotels-no-selection" type="text/html">
</script>
