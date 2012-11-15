<script type="text/html" id="flight-part-template">
    <table class="aviaTickets">
        <tbody>
        <tr>
            <td class="tdICO"></td>
            <td class="tdFrom">
                <div class="what">Вылет</div>
                <div class="city"><span data-bind="text: $data.departureCity()">Санкт-Петербург</span>, <span data-bind="text: $data.departureAirport()" class="airport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data.departureTime()">2:35</span>, <span class="date" data-bind="text: $data.departureDayMo()">28 мая</span></div>
            </td>
            <td class="tdPath">
                <div class="timeAndPath">
                    <div class="ico-path">
                        <span data-bind="html: $data.activeVoyage().stopoverHtml()"></span>
                    </div>
                    <div class="text">Всего <span data-bind="text: $data.duration()"></span></div>
                </div>
            </td>
            <td class="tdTo">
                <div class="what">Прилет</div>
                <div class="city"><span data-bind="text: $data.arrivalCity()">Санкт-Петербург</span>, <span class="airport" data-bind="text: $data.arrivalAirport()">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data.arrivalTime()">2:35</span>, <span class="date" data-bind="text: $data.arrivalDayMo()">28 мая</span></div>
            </td>
            <td class="tdAvia">
                <div class="airline" data-bind="text:$data.airlineName">Аэрофлот</div>
                <div class="voyage"><span data-bind="text: $data.flightCodesText">Рейс</span>: <span class="number" data-bind="html: $data.flightCodes()">S7-76</span></div>
                <div class="class">Класс: <span class="classMine">Эконом</span></div>
            </td>
            <td class="tdPrice">
                <div class="people" data-bind="text: $data.totalPeople">2 человека</div>
                <div class="price">12 500 <span class="rur">o</span></div>
                <div class="priceSale">
                    <!-- <div class="lastPrice">13 000 <span class="rur">o</span></div> <span class="icoTours"></span> -->
                </div>
                <div class="moreDetails"><a href="javascript:void(0);">Подробнее</a></div>
            </td>
        </tr>
        </tbody>
    </table>
</script>
