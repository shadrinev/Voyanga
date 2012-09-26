<script id="tours-results" type="text/html">
  <!-- LEFT BLOCK -->
  <div class="left-block">
    <!-- LEFT CONTENT -->
    <div class="left-content">	
      <h1>Мой маршрут</h1>
      <!-- UL TRIP -->
      <ul class="my-trip-list">
	<!-- FIXME change to repeat binding -->
	<!-- ko foreach: data -->
	<li class="items first">
	  <a href="#" data-bind="css: {fly: isAvia(), hotel: isHotel(), active: $parent.selection() == $data, toFrom: rt()}, click: $parent.setActive">
	    <div class="keys"></div>
	    <div class="path">
	      <div class="where" data-bind="html: destinationText()">С-Пб &rarr; Амстердам</div>
	      <div class="time"><span data-bind="html: priceText()"></span> <span data-bind="text: additionalText()">7:30 - 12:20</span></div>
	    </div>
	    <div class="date" data-bind="attr: {class: 'date '+ dateClass()}, html:dateHtml()">
	    </div>
	  </a>
	</li>
	<!-- /ko -->
	<!--li class="items">
	  <a href="#2" class="hotel active">
	    <div class="keys"></div>
	    <div class="path">
	      <div class="where">Отель в Амстердам</div>
	      <div class="time">12 750 <span class="rur">o</span>, Park Inn</div>
	    </div>
	    <div class="date orange-two">
	      <div class="day">
		<span class="f17">12</span>
		<br>
		мая
	      </div>
	      <div class="day">
		<span class="f17">24</span>
		<br>
		мая
	      </div>
	    </div>
	  </a>
	</li>
	<li class="items">
	  <a href="#3" class="fly">
	    <div class="keys"></div>
	    <div class="path">
	      <div class="where">С-Пб &rarr; Амстердам</div>
	      <div class="time">12 750 <span class="rur">o</span>, 7:30 - 12:20</div>
	    </div>
	    <div class="date orange-one">
	      <div class="day">
		<span class="f17">12</span>
		<br>
		мая
	      </div>
	    </div>
	  </a>
	</li -->
	<li class="items end">
	  <a href="#5" class="last">
	    <div class="keys"></div>
	    <div class="path">
	      Вся поездка
	    </div>
	  </a>
	</li>
      </ul>	
      <!-- END UL TRIP -->
      <span class="f14 bold" style="color:#2e333b;">Оформить</span>				
      <a href="#" class="btn-order">		
	<span class="cost" data-bind="text: price"></span> <span class="rur f26">o</span>
      </a>				
      <table class="finish-result">
	<tr>
	  <td class="txt">Общая стоимость:</td>
	  <td class="price">65 300 <span class="rur">o</span></td>
	</tr>
	<tr>
	  <td class="txt">Скидка за комплекс:</td>
	  <td class="price">4 800 <span class="rur">o</span></td>
	</tr>
      </table>
      <hr class="hr">
      <div class="voyasha-text">
	<div class="ico-voyasha"></div>
	<div class="inline-block">
	  <h3>Довериться Вояше</h3>
	  — Я составлю вашу поездку автоматически, просто укажите рамки бюджета
	</div>
      </div>
      <div class="voyasha-slider">
	<img src="images/img-slide.png">
      </div>
      <table class="finish-result">
	<tr>
	  <td class="txt">Мин. стоимость маршрута:</td>
	  <td class="price">30 300 <span class="rur">o</span></td>
	</tr>
	<tr>
	  <td class="txt">Макс. стоимость маршрута:</td>
	  <td class="price">110 000 <span class="rur">o</span></td>
	</tr>
      </table>
    </div>
    <!-- END LEFT CONTENT -->
  </div>
    <!-- ko template: {name: selection().template, data: selection().data} -->
    <!-- /ko -->
</script>
