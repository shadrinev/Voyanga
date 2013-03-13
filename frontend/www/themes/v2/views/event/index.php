<script type="text/html" id="event-index">
    <div class="center" data-bind="foreach: events">
        <div class="toursTicketsMain " data-bind="css: {active: active}, attr: {rel: image}">
            <div class="triangle" style="top: 0px;"><img src="/themes/v2/images/triangle.png"></div>
            <div class="innerTours">
                <div class="imgTours" data-bind='click: $parent.setActive'>
                    <img data-bind="attr: {src: thumb}">
                </div>
                <div class="textTours">
                    <a data-bind="attr:{href: eventPageUrl}"><span data-bind="text: title">Пять дней в столице Англии</span></a>
                    <div class="priceEvent"><a data-bind="attr:{href: eventPageUrl}">от <span class="price" data-bind="text: Utils.formatPrice( minimalPrice()() )">15 250</span> <span class="rur">o</span></a></div>
                </div>
            </div>
            <div class="l"></div>
            <div class="r"><a href="#1" class="lookEyes" data-bind="attr:{href: eventPageUrl}"></a></div>
        </div>
    </div>
</script>