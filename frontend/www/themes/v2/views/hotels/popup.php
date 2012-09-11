<script id="hotels-popup" type="text/html">
    <div class="hotel-details">
        <div class="title">
            <h1>Рэдиссон Соня Отель</h1>
            <div class="rating">
                <span class="value" data-bind="text: rating">4,5</span>
                <span class="text">рейтинг<br>отеля</span>
            </div>
            <div data-bind="attr: {class: 'stars ' + stars}"></div>
            <div class="clear"></div>
        </div>
        <div class="place-buy">
            <div class="street" data-bind="text:address">
                Санкт-Петербург, ул. Морская Набережная, д. 31/2
            </div>
            <ul class="tmblr">
                <li class="active"><span class="ico-descr"></span> <a href="#descr">Описание</a></li>
                <li><span class="ico-see-map"></span> <a href="#map">На карте</a></li>
            </ul>
            <div class="book">
                <div class="how-cost">
                    от <span class="cost">5 200</span><span class="rur f21">o</span> / ночь
                </div>
                <a href="#" class="btn-cost"><span class="l"></span><span class="text">Выбрать отель</span></a>
            </div>
        </div>
        <div class="tab" id="descr">
            <div class="photo-slide-hotel">
                <ul data-bind="foreach: photos">
                    <li><a href="#" data-bind="attr: {href: largeUrl}" class="photo"><img src="#" data-bind="attr:{src: smallUrl }"></a></li>
                </ul>
                Фотографии предоставлены отелями.
            </div>
            <div class="description">
                <div class="left">
                    <div class="right">
                        <h3>Отель на карте</h3>
                        <div class="map-hotel">
                            <img src="images/pic-map-popup.png">
                        </div>
                        Отель расположен в 10 км от центра
                    </div>
                    <h3>Описание отеля</h3>
                    <div class="text" data-bind="text: description">
                    </div>
                    <a href="02-HOTEL-WITH-OUT-FILTER.html" class="read-more">Подробнее</a>
                    <h3>Услуги в отеле</h3>
                    <ul>
                        <li><span class="ico-wi-fi"></span> бесплатный Wi-Fi</li>
                        <li><span class="ico-glass"></span> широкая коктейльная карта</li>
                        <li><span class="ico-dog"></span> можно с пёсиком</li>
                    </ul>
                </div>

            </div>
        </div>
        <div class="tab" id="map" style="display:none;">
            <div class="map-big">
                <img src="images/pic-big-map.png">
            </div>
            Отель расположен в 10 км от центра
        </div>
    </div>
</script>
