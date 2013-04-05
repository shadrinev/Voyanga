<script type="text/html" id="flight-rt-part-template">
    <div class="ticketBox">
    <table class="aviaTickets">
        <tbody>
        <tr>
            <td class="tdICO paddingBottom" rowspan="2">
                <div class="ico"></div>
            </td>
            <td class="tdFrom paddingBottom">
                <div class="what">Вылет</div>
                <div class="city"><span data-bind="text: $data.departureCity()">Санкт-Петербург</span>, <span data-bind="text: $data.departureAirport()" class="airport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data.departureTime()">2:35</span>, <span class="date" data-bind="text: $data.departureDayMo()">28 мая</span></div>
            </td>
            <td class="tdPath">
                <div class="timeAndPath" rel="сколько занимает пересадка <br> может и быть в две строчки">
                        <div class="ico-path" data-bind="click: $data.showDetailsOverview">
                            <span data-bind="html: $data.activeVoyage().stopoverHtml()"></span>
                        </div>
                    <div class="text" data-bind="click: $data.showDetailsOverview">Всего <span data-bind="text: $data.duration()"></span></div>
                </div>
            </td>
            <td class="tdTo paddingBottom">
                <div class="what">Прилет</div>
                <div class="city"><span data-bind="text: $data.arrivalCity()">Санкт-Петербург</span>, <span class="airport" data-bind="text: $data.arrivalAirport()">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data.arrivalTime()">2:35</span>, <span class="date" data-bind="text: $data.arrivalDayMo()">28 мая</span></div>
            </td>
            <td class="tdAvia paddingBottom">
                <div>
                    <span class="tooltip" data-bind="click: $data.showDetailsOverview, attr: {'rel': $data.firstAirlineName()}"><img data-bind="attr: {'src': '/img/airlines/' + $data.firstAirline() +'.png'}" ></span>
                </div>
                <div class="class">Класс: <span class="classMine" data-bind='text: $data.serviceClassReadable'>Эконом</span></div>
            </td>
        </tr>
        <tr>
            <td class="tdFrom">
                <div class="what">Вылет</div>
                <div class="city"><span data-bind="text: $data.rtDepartureCity()">Санкт-Петербург</span>, <span data-bind="text: $data.rtDepartureAirport()" class="airport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data.rtDepartureTime()">2:35</span>, <span data-bind="text: $data.rtDepartureDayMo()" class="date">28 мая</span></div>
            </td>
            <td class="tdPath">
                <div class="timeAndPath" rel="сколько занимает пересадка <br> может и быть в две строчки">
                    <div class="ico-path" data-bind="click: $data.showDetailsOverview">
                        <span data-bind="html: $data.activeVoyage().activeBackVoyage().stopoverHtml()"></span>
                    </div>
                    <div class="text" data-bind="click: $data.showDetailsOverview">Всего <span data-bind="text: $data.rtDuration()"></span></div>
                </div>
            </td>
            <td class="tdTo">
                <div class="what">Прилет</div>
                <div class="city"><span data-bind="text: $data.rtArrivalCity()">Санкт-Петербург</span>, <span data-bind="text: $data.rtArrivalAirport()" class="airport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data.rtArrivalTime()">2:35</span>, <span class="date" data-bind="text: $data.rtArrivalDayMo()">28 мая</span></div>
            </td>
            <td class="tdAvia">
                <div>
                    <span class="tooltip" data-bind="click: $data.showDetailsOverview, attr: {'rel': $data.rtFirstAirlineName()}"><img data-bind="attr: {'src': '/img/airlines/' + $data.rtFirstAirline() +'.png'}" ></span>
                </div>
                <div class="class">Класс: <span class="classMine" data-bind='text: $data.serviceClassReadable'>Эконом</span></div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="tdPrice">
        <div class="verticalAlign">
            <div class="price"><span data-bind="text:Utils.formatPrice($data.price)">12 500</span><span class="rur">o</span></div>
            <div class="people" data-bind="text: $data.totalPeopleGenAlmost">2 человека</div>
            <div class="priceSale">
                <!-- <div class="lastPrice">13 000 <span class="rur">o</span></div> <span class="icoTours"></span> -->
            </div>
        </div>
        <div class="moreDetails"><a href="#" data-bind="click: $data.showDetailsOverview">Подробнее</a></div>
    </div>
        <span class="lb"></span>
        <span class="rb"></span>
    </div>
</script>
