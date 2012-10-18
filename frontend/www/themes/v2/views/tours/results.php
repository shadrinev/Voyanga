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
        <li class="items" data-bind="css: {first: $index()==0}">
          <a href="#" data-bind="css: {fly: isAvia(), hotel: isHotel(), active: $parent.selection() == $data ||  $parent.selection().parent == $data, toFrom: rt()}, click: $parent.setActive">
            <div class="keys" data-bind="click: $parent.removeItem"></div>
            <div class="path">
              <div class="where" data-bind="html: destinationText()">С-Пб &rarr; Амстердам</div>
              <div class="time"><span data-bind="html: priceHtml()"></span> <span data-bind="text: additionalText()">7:30 - 12:20</span></div>
              <div class="alpha"></div>
            </div>
            <div class="date" data-bind="attr: {class: 'date '+ dateClass()}, html:dateHtml()">
            </div>
          </a>
        </li>
        <!-- /ko -->
        <li class="items end">
          <a href="#" class="last" data-bind="click: showOverview, css: {active: selection().overview}">
            <div class="keys"></div>
            <div class="path">
              Вся поездка
            </div>
          </a>
        </li>
      </ul>
      <!-- END UL TRIP -->
      <div id="tour-buy-btn" style="display: none">
	<span class="f14 bold" style="color:#2e333b;">Оформить</span>
	<a href="#" class="btn-order" data-bind="click: buy">
          <span class="cost" data-bind="text: price"></span> <span class="rur f26">o</span>
	</a>
      </div>
      <table class="finish-result">
        <tr>
          <td class="txt">Общая стоимость:</td>
          <td class="price"><span data-bind="text: price()+savings()">65 300</span> <span class="rur">o</span></td>
        </tr>
        <tr>
          <td class="txt">Скидка за комплекс:</td>
          <td class="price"><span data-bind="text: savings()">4 800</span> <span class="rur">o</span></td>
        </tr>
      </table>
      <hr class="hr">
      <div class="voyasha-text">
        <div class="ico-voyasha"></div>
        <div class="inline-block">
          <h3>Довериться Вояше</h3>
          — Я уже составила вашу поездку, просто укажите рамки бюджета
        </div>
      </div>
      <table class="finish-result voyasha">
        <tr>
          <td class="txt">Мин. стоимость маршрута:</td>
          <td class="price"><div>30 300 <span class="rur">o</span></div></td>
        </tr>
        <tr>
          <td class="txt">Оптимальный вариант:</td>
          <td class="price"><div>58 200 <span class="rur">o</span></div></td>
        </tr>
        <tr>
          <td class="txt">Роскошный вариант:</td>
          <td class="price"><div>112 140 <span class="rur">o</span></div></td>
        </tr>
      </table>
    </div>
    <!-- END LEFT CONTENT -->
  </div>
    <!-- ko template: {name: selection().template, data: selection().data} -->
    <!-- /ko -->
</script>
