<!--=== ===-->
<div class="oneBlock buyTicket">
    <!--=== ===-->
    <div class="paybuyContent">
        <h1>1. <?php echo $header ?></h1>
        <!-- ALL TICKETS DIV -->
        <div class="allTicketsDIV">
            <span data-bind="template: {name: 'items', data: itemsToBuy}"></span>
            <span class="lv"></span>
            <span class="rv"></span>
            <span class="lt"></span>
            <span class="rt"></span>
        </div>
        <!-- END ALL TICKETS DIV -->
        <div class="theSum">
            <div class="left">
                <span data-bind="text: itemsToBuy.people"></span>
            </div>
            <div class="right">
                Итоговая стоимость
                <div class="price"><span data-bind="text: Utils.formatPrice(itemsToBuy.totalCost)">37 500</span><span
                        class="rur">o</span></div>
            </div>
        </div>
        <!-- END -->
        <?php if ($flightCross): ?>
            <div class="andOthers" style="display: block;">
                <span class="priceSale"></span>
                Добавьте отель в <?php echo City::getCityByCode($flightParams['cityTo'])->casePre; ?> и получите скидку
                до 10% за комплексное бронирование
                <a href="#" class="btn-addItems" data-bind="attr:{href: itemsToBuy.crossUrlHref()}"><span
                        class="l"></span>Добавить отель</a>
            </div>
        <?php endif; ?>
    </div>
    <!--=== ===-->
</div>
<!--=== ===-->
