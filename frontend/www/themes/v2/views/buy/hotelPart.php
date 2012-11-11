<script type="text/html" id="hotel-part-template">
    <table class="hotelTickets">
        <tbody>
        <tr>
            <td class="tdICO"></td>
            <td class="tdHotel">
                <div class="what">Гостиница</div>
                <div class="nameHostel" data-bind="text:hotelName">Коринтия Крестовский Парк</div>
                <div class="howPlace">Двухместный номер люкс</div>
            </td>
            <td class="tdInfo">
                <div class="adress" data-bind="text:address">
                    <span class="name">Адрес:</span>
                    <span>Санкт-Петербург, Крестовский остров, 28/7</span>
                </div>
                <div class="dateFrom"><span class="name">Дата заезда:</span> 14 июля 2012, с 15:00</div>
                <div class="dateTo"><span class="name">Дата выезда:</span> 15 июля 2012, до 17:00</div>
            </td>
            <td class="tdPrice">
                <div class="price"><span data-bind="text:combinedPrice"><span class="rur">o</span></div>
                <div class="priceSale">
                    <div class="lastPrice"><span data-bind="text:combinedPrice"></span><span class="rur">o</span></div>
                    <span class="icoTours"></span>
                </div>
                <div class="moreDetails"><a href="javascript:void(0);">Подробнее</a></div>
            </td>
        </tr>
        </tbody>
    </table>
</script>
