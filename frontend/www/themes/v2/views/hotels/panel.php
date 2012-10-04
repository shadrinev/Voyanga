<script type="text/html" id="hotels-panel-template">
  <table class="hotel-table">
    <tr>
      <td class="td-input-hotel">
        <div class="data">
          <input class="input-path-hotel" type="text" placeholder="Город" data-bind="autocomplete: {source:'city/hotel_req/1', iata: city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}">
        </div>
        </td>
        <td class="td-people-hotel">
        <div class="how-many-man hotel">
          <!-- ko foreach: rooms -->
          <div class="content" data-bind="click: $parent.show">
            <span class="num" data-bind="text: $index() + 1"></span>
            <div class="man" data-bind="repeat: adults"></div>
            <div class="child" data-bind="repeat: children"></div>
          </div>
          <!-- /ko -->
          <div class="btn" data-bind="click: show"></div>
          <div class="popup">
            <!-- ko foreach: {data: roomsView, afterRender: afterRender } -->
            <div class="float">
              <!-- ko template: {name: 'room-template', foreach: $data} -->
              <!-- /ko -->
            </div>
            <!-- /ko -->
          </div>
        </div>
      </td>
      <td class="btnTD">
        <a class="btn-find" data-bind="click: navigateToNewSearch, visible: formFilled">Найти</a>
      </td>
    </tr>
  </table>
</script>
<script type="text/html" id="room-template">
  <div class="number-hotel">
  	<a href="#" class="del-hotel">удалить</a>
    <h5>Номер <span data-bind="text: $index() + 1"></span></h5>
    <div class="one-str">
      <div class="adults">
        <div class="inputDIV">
          <input type="text"  data-bind="value: adults, css:{active: adults}">
          <a href="#" class="plusOne" data-bind="click:plusOne" rel="adults">+</a>
          <a href="#" class="minusOne" data-bind="click:minusOne" rel="adults">-</a>
        </div>
        взрослых
      </div>
      <div class="childs">
        <div class="inputDIV">
          <input type="text" data-bind="value: children, css:{active: children}" name="adult2" >
          <a href="#" class="plusOne" data-bind="click:plusOne" rel="children">+</a>
          <a href="#" class="minusOne" data-bind="click:minusOne" rel="children">-</a>
        </div>
        детей от 12 до 18 лет
      </div>
    </div>
    <div class="one-str" data-bind="foreach: ages, visible: ages().length">
      <div class="ages">
        <input data-bind="value: $data, attr:{name: 'asd'+$index()}" >
        лет
      </div>
    </div>
    <a href="#" data-bind="click:$parents[1].addRoom, visible: ($index()+1)==$length()" class="addOtherRoom"><span class="ico-plus"></span>Добавить еще один номер</a>
  </div>
</script>
