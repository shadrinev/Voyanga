<script id="hotels-popup" type="text/html">
    <div class="hotel-details">
        <div class="title" id="hotels-popup-header1">
            <h1 data-bind="text: hotelName">Рэдиссон Соня Отель</h1>
            <div class="rating" data-bind="visible: rating!='-'">
                <span class="value" data-bind="text: rating">4,5</span>
                <span class="text">рейтинг<br>отеля</span>
            </div>
            <div data-bind="attr: {class: 'stars ' + stars}"></div>
            <div class="clear"></div>
        </div>
        <div class="place-buy" id="hotels-popup-header2">
            <div class="street" data-bind="text:address">
                Санкт-Петербург, ул. Морская Набережная, д. 31/2
            </div>
            <ul class="tmblr">
                <li class="active" id="hotels-popup-tumblr-description"><span class="ico-descr"></span> <a href="#descr" data-bind="click: showDescription">Описание</a></li>
                <li id="hotels-popup-tumblr-map"><span class="ico-see-map"></span> <a href="#map" data-bind="click: showMap">На карте</a></li>
            </ul>
            <div class="book">
                <div class="how-cost">
                    от <span class="cost" data-bind="text:cheapest">5 200</span><span class="rur f21">o</span> / ночь
                </div>
                <a href="#" class="btn-cost"><span class="l"></span><span class="text">Выбрать отель</span></a>
            </div>
        </div>
        <div class="tab" id="hotels-popup-description">
            <div class="slide">

            <div class="photo-slide-hotel">
                <ul data-bind="foreach: photos">
                    <li><a href="#" data-bind="attr: {href: largeUrl}" class="photo"><img src="#" data-bind="attr:{src: largeUrl }"></a></li>
                </ul>
                Фотографии предоставлены отелями.
            </div>
            <div class="left-navi"></div><div class="right-navi"></div><div class="left-opacity"></div><div class="right-opacity"></div>
            </div>
            <div class="description">
                <div class="left">
                    <div class="right">
                        <h3>Отель на карте</h3>
                        <div class="map-hotel">
                            <img src="" data-bind="attr: {src: smallMapUrl()}, click: showMap">
                        </div>
                        Отель расположен в <span data-bind="text: distanceToCenter">10</span> км от центра
                    </div>
                    <h3>Описание отеля</h3>
                    <div class="text" data-bind="text: description">
                    </div>
                    <a href="#" class="read-more" data-bind="click: readMore">Подробнее</a>
                    <!-- ko if: hasHotelServices -->
                    <h3>Услуги в отеле</h3>
                    <ul data-bind="foreach: hotelServices">
                        <li><span class="ico-wi-fi"></span> <span data-bind="text: $data"></span></li>
                    </ul>
                    <!-- /ko -->
                </div>

            </div>
        </div>
        <div class="tab" id="hotels-popup-map" style="display:none;">
            <div class="map-big" id="hotels-popup-gmap">
            </div>
            <div style="display:none">Отель расположен в <span data-bind="text: distanceToCenter">10</span> км от центра</div>
        </div>
    </div>
</script>
