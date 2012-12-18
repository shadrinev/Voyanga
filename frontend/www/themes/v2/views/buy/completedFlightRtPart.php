<script type="text/html" id="completed-flight-rt-part-template">
    <div class="ticketBox inactive">
        <table class="aviaTickets">
            <tbody>
            <tr>
                <td class="tdICO paddingBottom" rowspan="2">
                    <div class="ico"></div>
                </td>
                <td class="tdFrom paddingBottom">
                    <div class="what">Вылет</div>
                    <div class="city"><span data-bind="text: $data[0].departureCity()">Санкт-Петербург</span>, <span data-bind="text: $data[0].departureAirport()" class="airport">Пулково-1</span></div>
                    <div class="time"><span data-bind="text: $data[0].departureTime()">2:35</span>, <span class="date" data-bind="text: $data[0].departureDayMo()">28 мая</span></div>
                </td>
                <td class="tdPath">
                    <div class="timeAndPath" rel="сколько занимает пересадка <br> может и быть в две строчки">
                            <div class="ico-path">
                                <span data-bind="html: $data[0].activeVoyage().stopoverHtml()"></span>
                            </div>
                        <div class="text">Всего <span data-bind="text: $data[0].duration()"></span></div>
                    </div>
                </td>
                <td class="tdTo paddingBottom">
                    <div class="what">Прилет</div>
                    <div class="city"><span data-bind="text: $data[0].arrivalCity()">Санкт-Петербург</span>, <span class="airport" data-bind="text: $data[0].arrivalAirport()">Пулково-1</span></div>
                    <div class="time"><span data-bind="text: $data[0].arrivalTime()">2:35</span>, <span class="date" data-bind="text: $data[0].arrivalDayMo()">28 мая</span></div>
                </td>
                <td class="tdAvia paddingBottom">
                    <div class="airline" data-bind="text:$data[0].airlineName">Аэрофлот</div>
                    <div class="voyage"><span data-bind="text: $data[0].flightCodesText">Рейс</span>: <span class="number" data-bind="html: $data[0].flightCodes()">S7-76</span></div>
                    <div class="class">Класс: <span class="classMine">Эконом</span></div>
                </td>
            </tr>
            <tr>
                <td class="tdFrom">
                    <div class="what">Вылет</div>
                    <div class="city"><span data-bind="text: $data[0].rtDepartureCity()">Санкт-Петербург</span>, <span data-bind="text: $data[0].rtDepartureAirport()" class="airport">Пулково-1</span></div>
                    <div class="time"><span data-bind="text: $data[0].rtDepartureTime()">2:35</span>, <span data-bind="text: $data[0].rtDepartureDayMo()" class="date">28 мая</span></div>
                </td>
                <td class="tdPath">
                    <div class="timeAndPath" rel="сколько занимает пересадка <br> может и быть в две строчки">
                        <div class="ico-path">
                            <span data-bind="html: $data[0].activeVoyage().activeBackVoyage().stopoverHtml()"></span>
                        </div>
                        <div class="text">Всего <span data-bind="text: $data[0].rtDuration()"></span></div>
                    </div>
                </td>
                <td class="tdTo">
                    <div class="what">Прилет</div>
                    <div class="city"><span data-bind="text: $data[0].rtArrivalCity()">Санкт-Петербург</span>, <span data-bind="text: $data[0].rtArrivalAirport()" class="airport">Пулково-1</span></div>
                    <div class="time"><span data-bind="text: $data[0].rtArrivalTime()">2:35</span>, <span class="date" data-bind="text: $data[0].rtArrivalDayMo()">28 мая</span></div>
                </td>
                <td class="tdAvia">
                    <div class="airline" data-bind="text:$data[0].rtAirlineName">Аэрофлот</div>
                    <div class="voyage"><span data-bind="text:$data[0].rtFlightCodesText()">Рейс</span>: <span class="number" data-bind="html: $data[0].rtFlightCodes()">S7-76</span></div>
                    <div class="class">Класс: <span class="classMine">Эконом</span></div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="tdPrice">
            <div class="statusOrder">
                <span class="price">122 500</span><span class="rur">o</span> за 22 человека
                <div class="status wait">
                    <span data-bind='attr: {id: $data[0].key}' style="font-weight: bold">в обработке</span>
                </div>
            </div>
        </div>
    </div>
</script>
