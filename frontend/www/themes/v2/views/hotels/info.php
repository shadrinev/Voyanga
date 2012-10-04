<script type="text/html" id="hotels-info-template">
  <div class="main-block">
    <div id="content" data-bind="template: {name: 'hotels-info-inner', data: $data}" ></div>
  </div>
</script>
<script type="text/html" id="hotels-info-inner">
  <a href="#back" data-bind="click: back">НАЗАД</a>
  <div class="title">
    <h1 data-bind="text: hotelName">Рэдиссон Соня Отель</h1>
    <div class="rating" data-bind="visible: rating!='-'">
      <span class="value" data-bind="text: rating">4,5</span>
      <span class="text">рейтинг<br>отеля</span>
    </div>

    <div class="stars four" data-bind="attr: {class: 'stars ' + stars}"></div>

    <div class="clear"></div>
  </div>

  <div class="place-buy">
    <div class="street" data-bind="text: address">
      Санкт-Петербург, ул. Морская Набережная, д. 31/2
    </div>
    <ul class="tmblr">
      <li class="active" id="hotel-info-tumblr-description"><span class="ico-descr"></span> <a href="#descr" data-bind="click: showDescriptionInfo">Описание</a></li>
      <li id="hotel-info-tumblr-map"><span class="ico-see-map"></span> <a href="#map" data-bind="click: showMapInfo">На карте</a></li>
    </ul>
    <div class="book">
      <div class="how-cost">
        от <span class="cost" data-bind="text: cheapest">5 200</span><span class="rur f21">o</span> / ночь
      </div>
      <a class="btn-cost" href="#" data-bind="click:select, css: {selected: cheapestSet.resultId == activeResultId()}"><span class="l"></span><span class="text" data-bind="text: selectText"></span></a>
    </div>
  </div>
  <!-- DESCR -->
  <div class="descr" id="descr">
    <div class="left">
      <div class="right">
        <div class="map-hotel">
          <img src="" data-bind="attr:{src: smallMapUrl()}">
        </div>
        Отель расположен в <span data-bind="text: distanceToCenter">10</span> км от центра
      </div>
      <!-- ko if: numPhotos > 0 -->
      <div class="photo-slide-hotel">
        <ul data-bind="foreach: photos">
          <li><a href="#" class="photo" data-bind="attr:{href: largeUrl}"><img src="#" data-bind="attr:{src: largeUrl}"></a></li>
        </ul>
        Фотографии предоставлены отелями(<span data-bind="text: numPhotos"></span>).
      </div>
      <!-- /ko -->
      <div class="descr-text">
        <h3>Описание отеля</h3> <a href="#" class="translate">Перевести</a>
        <div class="text" data-bind="text:description">
        </div>
        <a href="#" class="read-more" data-bind="click: readMore">Подробнее</a>
      </div>
    </div>
  </div>
  <!-- END DESCR -->
  <!-- MAP -->
  <div class="descr" id="map" style="display: none">
    <div class="map-big" id="hotel-info-gmap">
    </div>
    Отель расположен в <span data-bind="text: distanceToCenter">10</span> км от центра
  </div>
  <!-- END MAP -->
  <!-- SERVICE -->
  <!-- ko if: hasHotelServices -->
  <div class="service-in-hotel">
    <h3>Услуги в отеле</h3>
    <ul data-bind="foreach: hotelServices">
      <li><span class="ico-wi-fi"></span> <span data-bind="text: $data"></span></li>
    </ul>
  </div>
  <!-- /ko -->

  <!-- ko if: hasRoomAmenities -->
  <div class="service-in-room">
    <h3>Услуги в номере</h3>
    <ul data-bind="foreach: roomAmenities">
      <li><span class="ico-wi-fi"></span> <span data-bind="text: $data"></span></li>
    </ul>
  </div>
  <!-- /ko -->
  <!-- END SERVICE -->
  <!-- INFO TRIP -->
  <div class="info-trip">
    <div class="date-trip">
      26 мая - 27 мая
    </div>
    <h2>Номера в <span data-bind="text: hotelName">Рэдиссон Соня Отель</span></h2>
    <h3>Рекомендуемые сочетания по вашему запросу</h3>
    <!-- ko foreach: roomSets -->
    <div class="block-trip">
      <table>
        <tbody>
          <tr>
            <td class="name">
              <ul data-bind="foreach: rooms">
                <li><span class="text" data-bind="text: name">Стандартный двухместный номер</span> <span data-bind="if: hasMeal"><span class="ico-breakfast"></span> <span data-bind="text: meal">Завтрак «шведский стол»</span></span></li>
              </ul>
              <a href="">Условия отмены бронирования</a>
            </td>
            <td class="button"><a class="btn-cost" href="#" data-bind="click:$parent.select, css: {selected: resultId == $parent.activeResultId()}"><span class="l"></span><span class="text" data-bind="text: $parent.selectText"></span><span class="cost" data-bind="text: price">14 200</span><span class="rur f21">o</span></a></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- /ko -->
  </div>
  <!-- END INFO TRIP -->
</div>
</script>
