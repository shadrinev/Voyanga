<script type="text/html" id="completed-hotel-part-template">
    <div class="ticketBox inactive">
    <table class="hotelTickets">
        <tbody>
        <tr>
            <td class="tdICO">
                <div class="ico"></div>
            </td>
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
        <div class="statusOrder">
                <span class="price">
                    <!-- ko if: $parent.tour -->
                        <span data-bind="text: Utils.formatPrice(roomSets()[0].discountPrice)">12 500</span> руб. за
                    <!-- /ko -->
                    <!-- ko ifnot: $parent.tour -->
                        <span data-bind="text: Utils.formatPrice(roomSets()[0].price)">12 500</span> руб. за
                    <!-- /ko -->
                    <span data-bind="text: $data.totalPeopleGen">2 человека</span>
                </span>
            <div class="status wait">
                <span data-bind='attr: {id: $data.key}' style="font-weight: bold">в обработке</span>
            </div>
        </div>
    </div>
        <span class="lb"></span>
        <span class="rb"></span>
    </div>
</script>
