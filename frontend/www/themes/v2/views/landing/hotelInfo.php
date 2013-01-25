<div class="center-block" >
<div class="main-block" style="width: 935px; margin-left: auto; margin-right: auto;">
<div id="content" style="width: 935px;">
<div class="title hotel">
    <h1><?php echo $hotelInfo->hotelName;?></h1>

    <div class="rating"  style="display: none;">
        <div class="textRating" onmouseover="ratingHoverActive(this)" onmouseout="ratingHoverNoActive(this)">
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
        <li class="active" id="hotel-info-tumblr-description"><span class="ico-descr"></span> <a href="#descr" data-bind="click: showDescriptionInfo">Описание</a></li>
        <li id="hotel-info-tumblr-map"><span class="ico-see-map"></span> <a href="#map" data-bind="click: showMapInfo">На карте</a></li>
    </ul>
    <div class="book">
        <div class="how-cost">
            от <span class="cost" data-bind="text: cheapestSet.pricePerNight">4389</span><span class="rur f21">o</span> / ночь
        </div>
        <a class="pressButton" href="#"><span class="l"></span><span class="text" data-bind="text: selectText">Забронировать</span></a>
    </div>
</div>
<!-- DESCR -->
<div class="descr" id="descr">
    <div class="left">
        <div class="right">
            <div class="map-hotel">
                <img src="http://maps.googleapis.com/maps/api/staticmap?zoom=13&amp;size=310x259&amp;maptype=roadmap&amp;markers=icon:http://test.voyanga.com/themes/v2/images/pin1.png%7Ccolor:red%7Ccolor:red%7C%7C<?php echo $hotelInfo->latitude.','.$hotelInfo->longitude;?>&amp;sensor=false">
            </div>

        </div>
        <!-- ko if: numPhotos > 0 -->
        <div class="photo-slide-hotel">
            <ul data-bind="foreach: photos,photoSlider: photos">
                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296463b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="0"><img src="http://hotelbook.ru/photos/118/118/4/37/1296463b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296464b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="1"><img src="http://hotelbook.ru/photos/118/118/4/37/1296464b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296465b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="2"><img src="http://hotelbook.ru/photos/118/118/4/37/1296465b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296466b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="3"><img src="http://hotelbook.ru/photos/118/118/4/37/1296466b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296467b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="4"><img src="http://hotelbook.ru/photos/118/118/4/37/1296467b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296468b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="5"><img src="http://hotelbook.ru/photos/118/118/4/37/1296468b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296469b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="6"><img src="http://hotelbook.ru/photos/118/118/4/37/1296469b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296470b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="7"><img src="http://hotelbook.ru/photos/118/118/4/37/1296470b.jpg" data-bind="attr:{src: largeUrl}"></a></li>

                <li><a href="http://hotelbook.ru/photos/118/118/4/37/1296471b.jpg" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="8"><img src="http://hotelbook.ru/photos/118/118/4/37/1296471b.jpg" data-bind="attr:{src: largeUrl}"></a></li>
            </ul><div class="left-navi" style="display: none;"></div><div class="right-navi" style=""></div>
            <div class="photoNumb">Фотографии предоставлены отелями.</div>
        </div>
        <!-- /ko -->
        <div class="descr-text">
            <h3>Описание отеля</h3>
            <div class="text">
                <span data-bind="html: limitDesc.startText"><?php echo str_replace("\n",'<br>',$hotelInfo->description);?></span>
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
<!-- ko if: hasHotelGroupServices -->
<div class="service-in-hotel">
    <div class="shadowHotel"><img src="/themes/v2/images/shadow-hotel.png"></div>
    <h3>Услуги в отеле</h3>
    <?php //print_r($serviceList);die();?>
    <?php foreach($serviceList as $group):?>
    <table class="serviceInHotelTable">
        <tbody><tr>
            <td class="title">
                <h3><span class="icoService <?php echo $group['icon'];?>"></span><span><?php echo $group['name'];?></span></h3>
            </td>
            <td class="list">
                <ul>
                    <?php foreach($group['elements'] as $element):?>
                        <li><span class="dotted"></span> <span><?php echo $element; ?></span></li>
                    <?php endforeach;?>
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
    <?php endforeach;?>
</div>
<!-- /ko -->

<!-- END SERVICE -->
<div class="hotel-important-info">
    <div class="shadowHotel"><img src="/themes/v2/images/shadow-hotel.png"></div>
    <h3>Важная информация</h3>
    <ul>
        <li><span class="span">Время заселения:</span> <span data-bind="text: checkInTime">14:00</span></li>
        <!-- ko if: site --><!-- /ko -->
        <!-- ko if: phone -->
        <li><span class="span">Телефон:</span> <span data-bind="text: phone">7-495-7887272</span></li>
        <!-- /ko -->
        <!-- ko if: fax -->
        <li><span class="span">Факс:</span> <span data-bind="text: fax">7-495-4822076</span></li>
        <!-- /ko -->
        <!-- ko if: email --><!-- /ko -->
        <!-- ko if: metroList.length -->
        <li><span class="span">Ближайшее метро:</span> <span data-bind="foreach: metroList"><span data-bind="text: $index() != 0 ? ', '+$data : $data">Владыкино</span><span data-bind="text: $index() != 0 ? ', '+$data : $data">, Петровско-Разумовская</span></span></li>
        <!-- /ko -->
        <!-- ko if: locations.length -->
        <li><span class="span">Месторасположение:</span> <span data-bind="foreach: locations"><span data-bind="text: $index() != 0 ? ', '+$data : $data">Near Centre</span></span></li>
        <!-- /ko -->
        <!-- ko if: numberFloors -->
        <li><span class="span">Число этажей:</span> <span data-bind="text: numberFloors">5</span></li>
        <!-- /ko -->
        <!-- ko if: builtIn -->
        <li><span class="span">Год постройки:</span> <span data-bind="text: builtIn">1956</span></li>
        <!-- /ko -->
    </ul>
</div>

</div>
</div>
</div>