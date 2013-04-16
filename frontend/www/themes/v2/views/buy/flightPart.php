<script type="text/html" id="flight-part-template">
    <div class="ticketBox">
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
                        <div class="ico-path" data-bind="click: $data.showDetailsOverview">
                            <span data-bind="html: $data.activeVoyage().stopoverHtml()"></span>
                        </div>
                        <div class="text" data-bind="click: $data.showDetailsOverview">В пути <span data-bind="text: $data.duration()"></span></div>
                    </div>
                </td>
                <td class="tdTo">
                    <div class="what">Прилет</div>
                    <div class="city"><span data-bind="text: $data.arrivalCity()">Санкт-Петербург</span>, <span class="airport" data-bind="text: $data.arrivalAirport()">Пулково-1</span></div>
                    <div class="time"><span data-bind="text: $data.arrivalTime()">2:35</span>, <span class="date" data-bind="text: $data.arrivalDayMo()">28 мая</span></div>
                </td>
                <td class="tdAvia">
                    <div>
                        <span class="tooltip" data-bind="click: $data.showDetailsOverview, attr: {'rel': $data.firstAirlineName()}"><img data-bind="attr: {'src': '/img/airlines/' + $data.firstAirline() +'.png'}" ></span>
                    </div>
                    <div class="class">Класс: <span class="classMine" data-bind='text: $data.serviceClassReadable'>Эконом</span></div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="tdPrice">
            <div class="verticalAlign">
                <div class="price"><span data-bind="text: Utils.formatPrice($data.price)">12 500</span><span class="rur">o</span></div>
                <div class="people" data-bind="text: $data.totalPeopleGenAlmost">2 человека</div>
            </div>
            <div class="moreDetails"><a href="#" data-bind="click: $data.showDetailsOverview">Подробнее</a></div>
        </div>
        <span class="lb"></span>
        <span class="rb"></span>
    </div>
</script>
