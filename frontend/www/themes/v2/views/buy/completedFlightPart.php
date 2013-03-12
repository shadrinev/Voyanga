 <script type="text/html" id="completed-flight-part-template">
    <div class="ticketBox inactive">
        <table class="aviaTickets">
            <tbody>
            <tr>
                <td class="tdICO">
                    <div class="ico"></div>
                </td>
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
                    <div class="class">Класс: <span class="classMine" data-bind='text: $data.serviceClassReadable'>Эконом</span></div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="tdPrice">
            <div class="statusOrder">
                <span class="price">
                    <span data-bind="text: Utils.formatPrice($data.price)">12 500</span> руб. за
                    <span data-bind="text: $data.totalPeopleGen">2 человека</span>
                </span>
                <div class="status wait">
                    <span data-bind='attr: {id: $data.key}' style="font-weight: bold">в обработке</span>
                </div>
            </div>
        </div>
    </div>
</script>
