<script type="text/html" id="hotels-body-popup-template">
<div id="hotels-body-popup" class="body-popup">
	<div id="layer">  
		<div class="pv_cont">
			<table cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td>
							<div id="pv_box">

								<div data-bind="template: {name: 'hotels-popup', data: data}"></div>
								<div id="boxClose" data-bind="click: close"></div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="pv_switch">
			
		</div>
	</div>
</div>
</script>

<script id="hotels-popup" type="text/html">
  <div class="hotel-details">
    <div class="title" id="hotels-popup-header1">
      <h1 data-bind="text: hotelName">Рэдиссон Соня Отель</h1>
      <div class="rating" data-bind="visible: rating">
	<div class="textRating" onmouseover="ratingHoverActive(this)" onmouseout="ratingHoverNoActive(this)">
          <span class="value" data-bind="text: rating"></span>
          <span class="text" data-bind="html: ratingName">рейтинг<br>отеля</span>
	</div>
	<div class="descrRating">
          <strong><span data-bind="text: rating"></span> из 5 баллов</strong>
          Рейтинг построен на основе анализа данных о качестве отеля и отзывах его посетителей.
	</div>
      </div>
      <div data-bind="attr: {'class': 'stars ' + stars}"></div>
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
          от <span class="cost" data-bind="text: Utils.formatPrice(cheapestSet.pricePerNight)">5 200</span><span class="rur f21">o</span> / ночь
        </div>
        <a href="#" class="pressButton" data-bind="click: parent.selectFromPopup, css:{selected: tours()?isActive():false}, visible: !window.toursOverviewActive"><span class="l"></span><span class="text" data-bind="text:selectText">Выбрать отель</span></a>
      </div>
    </div>
    <div class="tab" id="hotels-popup-description">
      <div class="slide">
        <div class="photo-slide-hotel">
          <ul data-bind="foreach: photos,photoSlider: photos">
            <li><a href="#" class="photo" data-bind="attr:{href: largeUrl,'data-photo-index': $index()},click: $parent.showPhoto" data-photo-index="0"><img src="#" data-bind="attr:{src: largeUrl}"></a></li>
          </ul>
        </div>
      </div>
      <div class="description">
        <div class="left">
          <div class="smallMaps">
            <h3>Отель на карте</h3>
            <div class="map-hotel">
              <img src="" data-bind="attr: {src: smallMapUrl()}, click: showMap">
            </div>
            Отель расположен в <span data-bind="text: distanceToCenter">10</span> км от центра
          </div>
          
          <h3>Описание отеля</h3>
          	<div class="text">
                  <span data-bind="html: limitDescPopup.startText"></span>
                  <span data-bind="visible: limitDescPopup.isBigText && showMoreDesc()">...</span>
                  <span class="endDesc" data-bind="html: limitDescPopup.endText" style="display: none"></span>
          	</div>
            <a href="#" class="read-more" data-bind="visible: limitDescPopup.isBigText,click: readMore,text: showMoreDescText">Подробнее</a>
          
	</div>
    </div>
      <!-- ko if: hasHotelGroupServices -->
      <div class="service-in-hotel">
        <h3>Услуги в отеле</h3>
        <div class="hideService">
          <!-- ko foreach: hotelGroupServices -->
          <table class="serviceInHotelTable">
            <tr>
              <td class="title">
                <h3><span class="icoService in-hotel" data-bind="attr: {'class': 'icoService '+groupIcon}"></span><span data-bind="text: groupName"></span></h3>
                </td>
              <td class="list">
                <ul data-bind="foreach: elements">
                  <li><span class="dotted"></span> <span data-bind="text: $data"></span></li>
                </ul>
              </td>
            </tr>
          </table>
          <!-- /ko -->
        </div>
        <div class="otherText">
          <a href="javascript:void(0)" onclick="readMoreService(this)"><span>Подробнее</span></a>
        </div>
      </div>
      <!-- /ko -->
    </div>
  </div>
    <div class="tab" id="hotels-popup-map" style="display:none;">
      <br>
      <div class="map-big" id="hotels-popup-gmap"></div>
      <div style="padding-left:20px;">Отель расположен в <span data-bind="text: distanceToCenter">10</span> км от центра</div>
    </div>
</div>
</script>
