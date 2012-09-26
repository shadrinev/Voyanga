<script type="text/html" id="avia-panel-template">
  <table class="panelTable AVIA">
    <tr>
      <td class="contTD">
        <div class="data">
          <div class="from" data-bind="click: showCalendar, css: {active: fromChosen()}">
            <input class="input-path departureCity" type="text">
            <input class="second-path departureCity" type="text" placeholder="Откуда" data-bind="autocomplete: {source:'city/airport_req/1', iata: departureCity, readable: departureCityReadable, readableAcc: departureCityReadableAcc, readableGen: departureCityReadableGen}">
              <div class="date">
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
          <div class="to" data-bind="click: showCalendar, css: {active: toChosen()}">
            <input class="input-path arrivalCity" type="text">
            <input class="second-path arrivalCity" placeholder="Куда" data-bind="autocomplete: {source:'city/airport_req/1', iata: arrivalCity, readable: arrivalCityReadable, readableAcc: arrivalCityReadableAcc, readableGen: arrivalCityReadableGen}">
            <div class="date">
              <span class="f17" data-bind="text: arrivalDateDay()">12</span>
              <br>
              <span class="month" data-bind="text: arrivalDateMonth()">мая</span>
            </div>
          </div>
        </div>
        <div class="how-many-man">
          <div class="content" data-bind="click: show">
            <!-- ko if: overall()>5 -->
            <!-- ko if: adults()>0 -->
            <div class="man"></div>
            <div class="count"><span>x</span><i data-bind="text: adults()"></i></div>
	    <!-- /ko -->
            <!-- ko if: (sum_children())>0 -->
            <div class="child"></div>
            <div class="count"><span>x</span><i data-bind="text: sum_children()"></i></div>
            <!-- /ko -->
            <!-- /ko -->
            <!-- ko if: overall()<=5 -->
            <div class="man" data-bind="repeat: adults()"></div>
            <div class="child" data-bind="repeat: sum_children()"></div>
            <!-- /ko -->
          </div>
          <div class="btn" data-bind="click: show"></div>
          <div class="popup">
            <div class="adults">
              <div class="inputDIV">
                <input type="text" name="adult" data-bind="css: {active: adults() > 0}, value: adults">
                <a href="#" class="plusOne" data-bind="click: plusOne" rel="adults">+</a>
                <a href="#" class="minusOne" data-bind="click: minusOne" rel="adults">-</a>
              </div>
              взрослых
            </div>
            <div class="childs">
              <div class="inputDIV">
                <input type="text" name="adult2" data-bind="css: {active: children() > 0}, value: children">
                <a href="#" class="plusOne" data-bind="click: plusOne" rel="children">+</a>
                <a href="#" class="minusOne" data-bind="click: minusOne" rel="children">-</a>
              </div>
              детей до 12 лет
            </div>
            <div class="small-childs">
              <div class="inputDIV">
                <input type="text" name="adult3" data-bind="css: {active: infants() > 0}, value: infants">
                <a href="#" class="plusOne" data-bind="click: plusOne" rel="infants">+</a>
                <a href="#" class="minusOne" data-bind="click: minusOne" rel="infants">-</a>
              </div>
              детей до 2 лет
            </div>
	    
          </div>
        </div>
      </td>
      <td class="btnTD">
        <a class="btn-find" data-bind="click: navigateToNewSearch">Найти</a>
      </td>
    </tr>
  </table>
<!-- BTN MINIMIZE -->
<a href="#" class="btn-minimizePanel" data-bind="css: {active: minimized()}, click:minimize">
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
