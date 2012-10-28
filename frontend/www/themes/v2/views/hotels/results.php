<?php
    //$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
    $images = '/themes/v2';
?>
<script id="hotels-results" type="text/html">
        <!-- MAIN BLOCK -->
        <div class="main-block">
            <div id="content" data-bind="template: {name: 'hotels-results-inner', data: results()}" >
            </div>
            <!-- END MAIN BLOCK -->
            <!-- FILTER BLOCK -->
            <div class="filter-block" data-bind="template: {name: 'hotels-filters', data: results().filters}">
            </div>
            <!-- END FILTER BLOCK -->
            <div class="clear"></div>
        </div>
        <!-- END ALL CONTENT -->
</script>

<script type="text-html" id="hotels-results-inner">
<div id="all-hotels-results">
<h1  data-bind="visible: true">Выберите отель в <span data-bind="text: city.casePre">Амстердам</span><div class="hideTitle">, <span data-bind="text: getDateInterval()">19-26 мая</span></div></h1>
<div class="ticket-content hotels">
    <h2>Найдено отелей: <span data-bind="text: numResults">##</span></h2>
    <div class="sorting-panel"><span class="hotel-sort-by">сортировать по:</span> <span data-bind="click: sortByPrice,attr:{class: sortByPriceClass()}">&nbsp;цене</span> <span data-bind="click: sortByRating,attr:{class: sortByRatingClass()}">&nbsp;рейтингу</span>  </div>
    <div class="clear"></div>
        <!-- ko foreach: resultsForRender -->
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
                        <a href="#"  data-bind="click: showMapDetails" class="in-the-map"><span class="ico-see-map"></span> <span class="link">На карте</span></a>
                    </div>
                    <div class="text">
                        <span data-bind="html: limitDesc.startText">Этот 4-звездочный отель расположен рядом с площадью Победы и парком Городов-Героев. К услугам гостей большой крытый бассейн и номера с телевизорами с плоским экраном...</span><span data-bind="visible: limitDesc.isBigText">...</span>
                    </div>
                </div>
                <div class="choose-a-hotel">
                    <div class="rating"  data-bind="visible: rating">
                    	<div class="textRating" onmouseover="ratingHoverActive(this)" onmouseout="ratingHoverNoActive(this)">
	                        <span class="value" data-bind="text: rating"></span>
	                        <span class="text" data-bind="html: ratingName">рейтинг<br>отеля</span>
                        </div>
                        <div class="descrRating">
                        	<strong><span data-bind="text: rating"></span> из 5 баллов</strong>
                        	Рейтинг построен на основе анализа данных о качестве отеля и отзывах его посетителей.
                        </div>
                    </div>
                    <a href="#" class="btn-cost" data-bind="click:$parent.select, css:{selected: tours() ? isActive():false}"><span class="l"></span><span class="text" data-bind="text:selectText">Выбрать отель</span></a>
                    <a class="details" data-bind="click: showDetails" href="#">Подробнее об отеле</a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="details">
                <ul data-bind="foreach: visibleRoomSets()">
                    <!-- ko if: $index() < 2 -->
                    <li  class="not-show" data-bind="template: {name: 'hotel-roomSet-template', data: $data}" />
                    <!-- /ko -->
                </ul>
                <div class="hidden-roomSets">
                    <ul data-bind="foreach: visibleRoomSets()">
                        <!-- ko if: $index() >= 2 -->
                        <li  class="not-show" data-bind="template: {name: 'hotel-roomSet-template', data: $data}" />
                        <!-- /ko -->
                    </ul>
                </div>
                <div class="tab-ul" data-bind="visible: visibleRoomSets().length > 2">
                    <a href="#" data-bind="click: showAllResults,text: showAllText(),attr:{class: isShowAll() ? 'active' : ''}">Посмотреть все результаты</a>
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
</div>
    <div id="all-hotels-map" style="display: none; height: 500px"></div>
</script>
<script id="photo-popup-template" type="text/html">
	<div id="body-popup-Photo">
		<div id="hotel-img-load">
			<img src="<?=   $images; ?>/images/load.gif">
		</div>
		<div id="titleNamePhoto">
			<h2 data-bind="text: title">Рэдиссон Соня Отель</h2>
			<div class="stars three" data-bind="attr:{class: 'stars '+ stars}"></div>
		</div>
    	<table>
    		<tr>
    			<td id="left" data-bind="attr:{class: (activeIndex()!=0) ? '' :'none'}, click: prev"></td>
    			<td id="center">
    				<div class="countAndClose">
    					<span data-bind="text: (activeIndex()+1)">11</span>
    					<span class="lost">/<span data-bind="text: (length0 +1)">17</span></span> 
    					<div id="boxClosePhoto" data-bind="click: close">Закрыть Х</div>
    				</div>
                    <div>
    				    <img data-bind="attr:{src: activePhoto()}, event: {load: photoLoad}, click: next" style="opacity:0">
                    </div>
                    <div class="namePhotoPopUp">
                    	Привет привет!
                    </div>
          		</td>
    			<td id="right" data-bind="attr:{class: (activeIndex()!=length0) ? '' :'none'}, click: next"></td>
    		</tr>
    	</table>
    	<div class="keyBoard"></div>
	</div>
</script>
<script id="hotel-roomSet-template" type="text/html">

        <div class="items">
            <table class="table-hotel-result">
                <tr>
                    <td class="td-float" data-bind="foreach: rooms">
                        <div class="float" >
                        
                        	<table>
                        		<tr>
                        			<td class="text" colspan="2">
                        				<span data-bind="text: name">Стандартный двухместный номер</span>
                        			</td>
                        		</tr>
                        		<tr>
                        			<td class="tdOrigText">
                        				<span data-bind="text: nameNemo" class="textOriginal"></span>
                        			</td>
                        			<td>
                      				 	<!-- ko if: hasMeal -->
			                            	<span class="ico-breakfast" data-bind="attr: {class: mealIcon}"></span> <span data-bind="text:meal">Завтрак</span>
			                            <!-- /ko -->
                        			</td>
                        		</tr>
                        	</table>
                        	
                        </div>
                    </td>
                    <td class="td-cost">
                        <div class="how-cost">
                            <span class="cost" data-bind="text: pricePerNight">14 200</span><span class="rur f21">o</span> / ночь <br> <span class="grey em" data-bind="visible: rooms.length == 2">За оба номера</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

</script>
