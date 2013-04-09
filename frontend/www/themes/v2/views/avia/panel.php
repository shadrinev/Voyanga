<script type="text/html" id="avia-panel-template">
    <table class="panelTable avia">
        <tr>
            <td class="tdCityStart" data-bind='css: {zero: !$root.in1()}'>
                <span data-bind="html: prefixText"></span>
            </td>
            <td class="tdCity">
                <div class="data">
                    <div class="from" data-bind="css: {active: fromChosen}">
                        <div class="bgInput">
                            <div class="left"></div>
                            <div class="center"></div>
                            <div class="right"></div>
                        </div>
                        <input class="input-path departureCity" type="text" tabindex="-1">
                        <input class="second-path departureCity" type="text" placeholder="Откуда"
                               data-bind="autocomplete: {name:'avia', source:'city/airport_req/1', iata: departureCity, readable: departureCityReadable, readableAcc: departureCityReadableAcc, readableGen: departureCityReadableGen, readablePre: departureCityReadablePre}">

                        <div class="date" data-bind="click: showCalendar">
                            <span class="f17" data-bind="text: departureDateDay()">12</span>
                            <br>
                            <span class="month" data-bind="text: departureDateMonth()">мая</span>
                        </div>
                    </div>
            </td>
            <td class="tdTumblr">
                <div class="tumblr">
                    <label for="there-back">
                        <div class="one" data-bind="css: {active: !rt()}, click: selectOneWay"></div>
                        <div class="two" data-bind="css: {active: rt()}, click: selectRoundTrip"></div>
                        <div class="switch"></div>
                    </label>
                    <input id="there-back" type="checkbox" data-bind="checked: rt()">
                </div>
            </td>
            <td class="tdCity">
                <div class="data">
                    <div class="to" data-bind="css: {active: rtFromChosen}">
                        <div class="bgInput">
                            <div class="left"></div>
                            <div class="center"></div>
                            <div class="right"></div>
                        </div>
                        <input class="input-path arrivalCity" type="text" tabindex="-1">
                        <input class="second-path arrivalCity" placeholder="Куда"
                               data-bind="autocomplete: {source:'city/airport_req/1', iata: arrivalCity, readable: arrivalCityReadable, readableAcc: arrivalCityReadableAcc, readableGen: arrivalCityReadableGen, readablePre: arrivalCityReadablePre}">

                        <div class="date" data-bind="click: showCalendar">
                            <span class="f17" data-bind="text: rtDateDay()">12</span>
                            <br>
                            <span class="month" data-bind="text: rtDateMonth()">мая</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="tdPeople">
                <span data-bind="template: {name: passengers.template, data: passengers}"></span>
            </td>
            <td class="tdButton">
                <a class="btn-find" data-bind="click: navigateToNewSearch, css: {inactive: formNotFilled}">Найти</a>
            </td>
        </tr>
    </table>
    <!-- BTN MINIMIZE -->
    <a href="#" class="btn-minimizePanel"
       data-bind="visible: !$root.indexMode(), css: {active: minimized()}, click:minimize">
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
