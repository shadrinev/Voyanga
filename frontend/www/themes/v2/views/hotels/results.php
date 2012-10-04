<?php
    $images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
?>
<script id="hotels-results" type="text/html">
        <!-- MAIN BLOCK -->
        <div class="main-block">
            <div id="content" data-bind="template: {name: 'hotels-results-inner', data: results()}" >
            </div>
            <!-- END MAIN BLOCK -->
            <!-- FILTER BLOCK -->
            <!-- div class="filter-block" data-bind="template: {name: 'hotels-filters', data: results().filters}">
            </div -->
            <!-- END FILTER BLOCK -->
            <div class="clear"></div>
        </div>
        <!-- END ALL CONTENT -->
</script>

<script type="text-html" id="hotels-results-inner">
<h1  data-bind="visible:tours">Выберите отель в <span>Амстердам</span>, 19-26 мая</h1>
<div class="ticket-content hotels">
    <h2>Найдено отелей: ##</h2>
    <div class="clear"></div>
        <!-- ko foreach: data -->
        <div class="hotels-tickets" data-bind="visible: visible()">
            <div class="content">
                <div class="full-info">
                    <div class="preview-photo">
                        <ul>
                            <li><a href="#" data-bind="click: showPhoto,attr: {'href': frontPhoto.largeUrl}" class="photo"><img data-bind="attr:{src: frontPhoto.largeUrl}"></a></li>
                        </ul>
                        <div class="how-much" data-bind="visible: numPhotos">
                            <a href="#">Фотографий (<span data-bind="text: numPhotos">11</span>)</a>
                        </div>
                    </div>
                    <div class="description">
                    <div class="title">
                        <h2><span data-bind="text:hotelName">Рэдиссон Соня Отель</span> <span class="gradient"></span></h2>
                        <div data-bind="attr: {class: 'stars ' + stars}"></div>
                    </div>
                    <div class="place">
                        <div class="street">
                            <span data-bind="text:address">Санкт-Петребург. ул. Морская Набережная, 31/2</span>
                            <span class="gradient"></span>
                        </div>
                        <a href="#"  data-bind="click:showMapDetails" class="in-the-map"><span class="ico-see-map"></span> <span class="link">На карте</span></a>
                    </div>
                    <div class="text" data-bind="text:description">
                        Этот 4-звездочный отель расположен рядом с площадью Победы и парком Городов-Героев. К услугам гостей большой крытый бассейн и номера с телевизорами с плоским экраном...
                    </div>
                </div>
                <div class="choose-a-hotel">
                    <div class="rating"  data-bind="visible: rating!='-'">
                        <span class="value" data-bind="text: rating"></span>
                        <span class="text">рейтинг<br>отеля</span>
                    </div>
                    <a href="#" class="btn-cost" data-bind="click:$parent.select, css:{selected: tours()?$parents[2].selection().activeHotel()==hotelId:false}"><span class="l"></span><span class="text" data-bind="text:selectText">Выбрать отель</span></a>
                    <a class="details" data-bind="click: showDetails" href="#">Подробнее об отеле</a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="details">
                <ul data-bind="foreach: roomSets">
                    <!-- ko if: visible -->
                    <li class="not-show">
                        <div class="items">
                            <div class="float" data-bind="foreach: rooms">
                                <span class="text"><span data-bind="text: name">Стандартный двухместный номер</span> <span data-bind="text: nameNemo"></span></span>
                                <!-- ko if: hasMeal -->
                                 <span class="ico-breakfast"></span> <span data-bind="text:meal">Завтрак</span>
                                <!-- /ko -->
                                <br>
                            </div>
                            <div class="how-cost">
                                <span class="cost" data-bind="text: pricePerNight">14 200</span><span class="rur f21">o</span> / ночь <br> <span class="grey em" data-bind="visible: rooms.length == 2">За оба номера</span>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </li>
                    <!-- /ko -->
                </ul>
                <div class="tab-ul" data-bind="visible: roomSets.length > 2">
                    <a href="#">Посмотреть все результаты</a>
                </div>
                <span class="lv"></span>
                <span class="rv"></span>
            </div>
        </div>
        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="bh"></span>
    </div>
    <!-- /ko -->
</div>
</script>
<script id="photo-popup-template" type="text/html">
  <div id="body-popup-Photo">
    <div id="popupPhoto">
      <div id="hotel-img-load">
	<img src="<?=   $images; ?>/images/load.gif">
      </div>
      <div id="photoBox">
	<div class="left" data-bind="visible: activeIndex()!=0, click: prev"></div>
	<div class="right" data-bind="visible: activeIndex()!=length0, click: next"></div>
	<div id="titleNamePhoto">
		<h2>Рэдиссон Соня Отель</h2>
		<div class="stars three"></div>
	</div>
	<div id="imgContent">
			<div class="countAndClose">11<span class="lost">/17</span> <div id="boxClosePhoto" data-bind="click: close">Закрыть Х</div></div>
          <img data-bind="attr:{src: activePhoto()}, event: {load: photoLoad}, click: next" style="opacity:0">
          	
	</div>
		<div class="keyBoard"></div>
      </div>
    </div>
    
  </div>
</script>
