<script type="text/html" id="hotel-part-template">
    <div class="ticketBox">
    <table class="hotelTickets">
        <tbody>
        <tr>
            <td class="tdICO"></td>
            <td class="tdHotel">
                <div class="what">Гостиница</div>
                <div class="nameHostel" data-bind="text:hotelName">Коринтия Крестовский Парк</div>
                <!-- ko foreach: roomSets -->
                    <!-- ko foreach: rooms -->
                        <div class="howPlace" data-bind="text: name">Двухместный номер люкс</div>
                    <!-- /ko -->
                <!-- /ko -->
            </td>
            <td class="tdInfo">
                <div class="adress" data-bind="text:address">
                    <span class="name">Адрес:</span>
                    <span>Санкт-Петербург, Крестовский остров, 28/7</span>
                </div>
                <div class="dateFrom"><span class="name">Дата заезда:</span> <span data-bind="text:checkInText"></span></div>
                <div class="dateTo"><span class="name">Дата выезда:</span> <span data-bind="text:checkOutText"></span></div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="tdPrice">
        <div class="verticalAlign">
            <div class="price"><span data-bind="text: roomSets()[0].discountPrice"></span><span class="rur">o</span></div>
            <div class="priceSale">
                <div class="lastPrice"><span data-bind="text: roomSets()[0].price"></span><span class="rur">o</span></div>
                <span class="icoTours"></span>
            </div>
        </div>
        <div class="moreDetails"><a href="#" data-bind="click: showDetails">Подробнее</a></div>
    </div>
    </div>
</script>
