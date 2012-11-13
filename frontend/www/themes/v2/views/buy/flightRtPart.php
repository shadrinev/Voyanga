<script type="text/html" id="flight-rt-part-template">
    <table class="aviaTickets">
        <tbody>
        <tr>
            <td class="tdICO paddingBottom" rowspan="2"></td>
            <td class="tdFrom paddingBottom">
                <div class="what">Вылет</div>
                <div class="city"><span data-bind="text: $data[0].departureCity">Санкт-Петербург</span>, <span data-bind="text: $data[0].departureAirport" class="airport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data[0].departureTime()">2:35</span>, <span class="date" data-bind="text: $data[0].departureDayMo()">28 мая</span></div>
            </td>
            <td class="tdPath paddingBottom">
                    <div class="timeAndPath" rel="сколько занимает пересадка <br> может и быть в две строчки">
                        <div class="ico-path"><span class="cup"></span><span class="cup"></span><span class="cup"></span></div>
                        <div class="text">Всего <span data-bind="text: $data[0].duration()"></span></div>
                    </div>
            </td>
            <td class="tdTo paddingBottom">
                <div class="what">Прилет</div>
                <div class="city"><span data-bind="text: $data[0].arrivalCity">Санкт-Петербург</span>, <span class="airport" data-bind="text: $data[0].arrivalAirport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data[0].arrivalTime()">2:35</span>, <span class="date" data-bind="text: $data[0].arrivalDayMo()">28 мая</span></div>
            </td>
            <td class="tdAvia paddingBottom">
                <div class="airline" data-bind="text:$data[0].airlineName">Аэрофлот</div>
                <div class="voyage">Рейс: <span class="number" data-bind="text: $data[0].flightCode">S7-76</span></div>
                <div class="class">Класс: <span class="classMine">Эконом</span></div>
            </td>
            <td class="tdPrice paddingBottom" rowspan="2">
                <div class="price">12 500 <span class="rur">o</span></div>
                <div class="priceSale">
                    <!-- <div class="lastPrice">13 000 <span class="rur">o</span></div> <span class="icoTours"></span> -->
                </div>
                <br>

                <div class="moreDetails"><a href="#" data-bind="click: showDetails">Подробнее</a></div>
            </td>
        </tr>
        <tr>
            <td class="tdFrom">
                <div class="what">Вылет</div>
                <div class="city"><span data-bind="text: $data[1].departureCity">Санкт-Петербург</span>, <span data-bind="text: $data[1].departureAirport" class="airport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data[1].departureTime()">2:35</span>, <span data-bind="text: $data[1].departureDayMo()" class="date">28 мая</span></div>
            </td>
            <td class="tdPath" data-bind="text: $data[1].duration()">
                <div class="timeAndPath" rel="сколько занимает пересадка <br> может и быть в две строчки">
                    <div class="ico-path"><span class="cup"></span><span class="cup"></span><span class="cup"></span></div>
                    <div class="text">Всего <span data-bind="text: $data[0].duration()"></span></div>
                </div>
            </td>
            <td class="tdTo">
                <div class="what">Прилет</div>
                <div class="city"><span data-bind="text: $data[1].arrivalCity">Санкт-Петербург</span>, <span data-bind="text: $data[1].arrivalAirport" class="airport">Пулково-1</span></div>
                <div class="time"><span data-bind="text: $data[1].arrivalTime()">2:35</span>, <span class="date" data-bind="text: $data[1].arrivalDayMo()">28 мая</span></div>
            </td>
            <td class="tdAvia">
                <div class="airline" data-bind="text:$data[1].airlineName">Аэрофлот</div>
                <div class="voyage">Рейс: <span class="number" data-bind="text: $data[1].flightCode">S7-76</span></div>
                <div class="class">Класс: <span class="classMine">Эконом</span></div>
            </td>
        </tr>
        </tbody>
    </table>
</script>
