<script type="text/html" id="tours-index">
    <div class="center" data-bind="foreach: events">
        <div class="toursTicketsMain " data-bind="css: {active: active}, attr: {rel: image}, click: $parent.setActive">
            <div class="triangle" style="top: 0px;"><img src="/themes/v2/images/triangle.png"></div>
            <div class="innerTours">
                <div class="imgTours">
                    <img data-bind="attr: {src: thumb}">
                </div>
                <div class="textTours">
                    <span data-bind="text: title">Пять дней в столице Англии</span><br>
                    от <span class="price" data-bind="text: minimalPrice()">15 250</span> <span class="rur">o</span>
                </div>
            </div>
            <div class="l"></div>
            <div class="r"><a href="#1" class="lookEyes"></a></div>
        </div>
    </div>
</script>