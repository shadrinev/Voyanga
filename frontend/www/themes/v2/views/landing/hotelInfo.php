<script type="text/javascript">
    window.hotPhotos = <?php echo json_encode($hotelInfo->images)?>;
    window.hotInfo = <?php echo json_encode(array('lat' => $hotelInfo->latitude, 'lng' => $hotelInfo->longitude, 'hotelName' => $hotelInfo->hotelName))?>;
    console.log('hot hotels', window.hotPhotos);
    initLandingPage = function () {
        var app, avia, hotels, tour;
        window.voyanga_debug = function () {
            var args;
            args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
            return console.log.apply(console, args);
        };
        app = new Application();
        avia = new AviaModule();
        hotels = new HotelsModule();
        tour = new ToursModule();
        window.app = app;
        app.register('tours', tour, true);
        app.register('hotels', hotels);
        app.register('avia', avia);
        app.runWithModule('hotels');
        app.activeModule('hotels');
        app.photos = window.hotPhotos;
        app.numPhotos = window.hotPhotos.length;
        window.mapInitialized = false;
        app.showMapInfo = function (context, event) {
            // FIXME FIXME FIMXE why this code navigates if we wont stop default?
            event.preventDefault();
            var el = $('#hotel-info-tumblr-map');
            if (el.hasClass('active'))
                return false;
            $('.place-buy .tmblr li').removeClass('active');
            el.addClass('active');
            $('#descr').hide();
            $('#map').show();
            if (!window.mapInitialized) {
                var coords = new google.maps.LatLng(window.hotInfo.lat, window.hotInfo.lng);
                var mapOptions = {center:coords, zoom:12, mapTypeId:google.maps.MapTypeId.ROADMAP};
                var map = new google.maps.Map($('#hotel-info-gmap')[0], mapOptions);
                var marker = new google.maps.Marker({
                    position:coords,
                    map:map,
                    icon:'http://voyanga.com/themes/v2/images/pin1.png',
                    title:window.hotInfo.hotelName
                });

            }
            window.mapInitialized = true;
        };
        app.showDescriptionInfo = function (context, event) {
            var el = $('#hotel-info-tumblr-description');
            if (el.hasClass('active'))
                return false;
            $('.place-buy .tmblr li').removeClass('active');
            el.addClass('active');
            $('#map').hide();
            $('#descr').show();
            $('#boxContent').css('height', 'auto');
        };
        ko.applyBindings(app);
        ko.processAllDeferredBindingUpdates();
    };
    $(document).ready(function () {
        initLandingPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })
</script>
<div class="center-block">
    <div class="main-block" style="width: 935px; margin-left: auto; margin-right: auto;">
        <div id="content" style="width: 935px;">
            <div class="title hotel" style="margin-top: 0px">
                <h1><?php echo $hotelInfo->hotelName;?></h1>

                <div class="rating" style="display: none;">
                    <div class="textRating" onmouseover="ratingHoverActive(this)"
                         onmouseout="ratingHoverNoActive(this)">
                        <span class="value"><?php echo $hotelInfo->rating;?></span>
                        <span class="text">рейтинг<br>отеля</span>
                    </div>
                    <div class="descrRating">
                        <strong><?php echo $hotelInfo->rating;?> из 5 баллов</strong>
                        Рейтинг построен на основе анализа данных о качестве отеля и отзывах его посетителей.
                    </div>
                </div>


                <div class="stars <?php echo $hotelInfo->getWordStars();?>"></div>

                <div class="clear"></div>
            </div>

            <div class="place-buy">
                <div class="street"><?php echo $hotelInfo->address;?></div>
                <ul class="tmblr">
                    <li class="active" id="hotel-info-tumblr-description"><span class="ico-descr"></span> <a
                        href="#descr" data-bind="click: showDescriptionInfo">Описание</a></li>
                    <li id="hotel-info-tumblr-map"><span class="ico-see-map"></span> <a href="#map"
                                                                                        data-bind="click: showMapInfo">На
                        карте</a></li>
                </ul>
                <div class="book">
                    <div class="how-cost">
                        от <span class="cost">4389</span><span class="rur f21">o</span> / ночь
                    </div>
                    <a class="pressButton"
                       href="/#hotels/search/<?php echo $city->code . '/' . date('d.m.Y', time() + 3600 * 24) . '/' . date('d.m.Y', time() + 3600 * 24 * 3);?>/2:0:0/"><span
                        class="l"></span><span class="text">Забронировать</span></a>
                </div>
            </div>
            <!-- DESCR -->
            <div class="descr" id="descr">
                <div class="left">
                    <div class="right">
                        <div class="map-hotel">
                            <img
                                src="//maps.googleapis.com/maps/api/staticmap?zoom=13&amp;size=310x259&amp;maptype=roadmap&amp;markers=icon:http://voyanga.com/themes/v2/images/pin1.png%7Ccolor:red%7Ccolor:red%7C%7C<?php echo $hotelInfo->latitude . ',' . $hotelInfo->longitude;?>&amp;sensor=false">
                        </div>

                    </div>
                    <!-- ko if: numPhotos > 0 -->
                    <div class="photo-slide-hotel">
                        <ul data-bind="foreach: photos,photoSlider: photos">
                            <li><a href="#" class="photo"
                                   data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto"
                                   data-photo-index="0"><img src="#" data-bind="attr:{src: largeUrl}"></a></li>
                        </ul>
                        <div class="photoNumb">Фотографии предоставлены отелями.</div>
                    </div>
                    <!-- /ko -->
                    <div class="descr-text">
                        <h3>Описание отеля</h3>

                        <div class="text">
                            <span><?php echo str_replace("\n", '<br>', $hotelInfo->description);?></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END DESCR -->
            <!-- MAP -->
            <div class="descr" id="map" style="display: none">
                <div class="map-big" id="hotel-info-gmap">
                </div>
            </div>
            <!-- END MAP -->
            <!-- INFO TRIP -->
            <div class="info-trip">

            </div>
            <!-- END INFO TRIP -->
            <!-- SERVICE -->
            <div class="service-in-hotel">
                <div class="shadowHotel"><img src="/themes/v2/images/shadow-hotel.png"></div>
                <h3>Услуги в отеле</h3>
                <?php //print_r($serviceList);die();?>
                <?php foreach ($serviceList as $group): ?>
                <table class="serviceInHotelTable">
                    <tbody>
                    <tr>
                        <td class="title">
                            <h3><span
                                class="icoService <?php echo $group['icon'];?>"></span><span><?php echo $group['name'];?></span>
                            </h3>
                        </td>
                        <td class="list">
                            <ul>
                                <?php foreach ($group['elements'] as $element): ?>
                                <li><span class="dotted"></span> <span><?php echo $element; ?></span></li>
                                <?php endforeach;?>
                            </ul>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php endforeach;?>
            </div>

            <!-- END SERVICE -->
            <div class="hotel-important-info">
                <div class="shadowHotel"><img src="/themes/v2/images/shadow-hotel.png"></div>
                <h3>Важная информация</h3>
                <ul>
                    <li><span class="span">Время заселения:</span>
                        <span><?php echo $hotelInfo->earliestCheckInTime; ?></span></li>
                    <?php if ($hotelInfo->site): ?>
                    <li><span class="span">Site:</span> <span><?php echo $hotelInfo->site; ?></span></li>
                    <?php endif;?>
                    <?php if ($hotelInfo->phone): ?>
                    <li><span class="span">Телефон:</span> <span><?php echo $hotelInfo->phone; ?></span></li>
                    <?php endif;?>
                    <?php if ($hotelInfo->fax): ?>
                    <li><span class="span">Факс:</span> <span><?php echo $hotelInfo->fax; ?></span></li>
                    <?php endif;?>
                    <?php if ($hotelInfo->email): ?>
                    <li><span class="span">Email:</span> <span><?php echo $hotelInfo->email; ?></span></li>
                    <?php endif;?>
                    <?php if ($hotelInfo->metroList): ?>
                    <li><span class="span">Ближайшее метро:</span>
                        <span><?php echo implode(', ', $hotelInfo->metroList); ?></span></li>
                    <?php endif;?>
                    <?php if ($hotelInfo->locations): ?>
                    <li><span class="span">Месторасположение:</span>
                        <span><?php echo implode(', ', $hotelInfo->locations); ?></span></li>
                    <?php endif;?>
                    <?php if ($hotelInfo->numberFloors): ?>
                    <li><span class="span">Число этажей:</span> <span><?php echo $hotelInfo->numberFloors; ?></span>
                    </li>
                    <?php endif;?>
                    <?php if ($hotelInfo->builtIn): ?>
                    <li><span class="span">Год постройки:</span> <span><?php echo $hotelInfo->builtIn; ?></span></li>
                    <?php endif;?>
                </ul>
            </div>

        </div>
    </div>
</div>