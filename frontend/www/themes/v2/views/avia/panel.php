<script type="text/html" id="avia-panel-template">
  <table class="panelTable AVIA">
    <tr>
      <td class="contTD">
        <div class="data">
          <div class="from" data-bind="css: {active: fromChosen}">
            <input class="input-path departureCity" type="text"  tabindex="-1">
            <input class="second-path departureCity" type="text" placeholder="Откуда" data-bind="autocomplete: {source:'city/airport_req/1', iata: departureCity, readable: departureCityReadable, readableAcc: departureCityReadableAcc, readableGen: departureCityReadableGen}">
              <div class="date" data-bind="click: showCalendar">
              <span class="f17" data-bind="text: departureDateDay()">12</span>
              <br>
              <span class="month" data-bind="text: departureDateMonth()">мая</span>
            </div>
          </div>
          <div class="tumblr">
            <label for="there-back">
              <div class="one" data-bind="css: {active: !rt()}, click: selectOneWay"></div>
              <div class="two" data-bind="css: {active: rt()}, click: selectRoundTrip"></div>
              <div class="switch"></div>
            </label>
            <input id="there-back" type="checkbox" data-bind="checked: rt()">
          </div>
          <div class="to" data-bind="css: {active: rtFromChosen}">
            <input class="input-path arrivalCity" type="text"  tabindex="-1">
            <input class="second-path arrivalCity" placeholder="Куда" data-bind="autocomplete: {source:'city/airport_req/1', iata: arrivalCity, readable: arrivalCityReadable, readableAcc: arrivalCityReadableAcc, readableGen: arrivalCityReadableGen}">
            <div class="date" data-bind="click: showCalendar">
              <span class="f17" data-bind="text: rtDateDay()">12</span>
              <br>
              <span class="month" data-bind="text: rtDateMonth()">мая</span>
            </div>
          </div>
        </div>
        <span data-bind="template: {name: passengers.template, data: passengers}"></span>
      </td>
      <td class="btnTD">
        <a class="btn-find" data-bind="click: navigateToNewSearch, visible: formFilled">Найти</a>
      </td>
    </tr>
  </table>
<!-- BTN MINIMIZE -->
<a href="#" class="btn-minimizePanel" data-bind="visible: !indexMode(), css: {active: minimized()}, click:minimize">
    <!-- ko if: minimized() -->
    <span></span> развернуть
    <!-- /ko -->
    <!-- ko if: !minimized() -->
    <span></span> свернуть
    <!-- /ko -->
</a>
<div class="minimize-rcomended">
    <a href="#" class="btn-minimizeRecomended" data-bind="click: returnRecommend"> вернуть рекомендации</a>
</div>

</script>
