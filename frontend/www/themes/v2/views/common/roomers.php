<script type="text/html" id="roomers-template">
  <div class="how-many-man hotel" >
    <div class="wrapDivContent"  data-bind="click: show">
        <!-- ko foreach: roomContainer.getRooms() -->
        <div class="content">
          <span class="num" data-bind="text: $index() + 1"></span>
          <div class="man" data-bind="repeat: adults"></div>
          <div class="child" data-bind="repeat: children"></div>
        </div>
        <!-- /ko -->
        <div class="btn"></div>
    </div>

    <div class="popup">
      <!-- ko foreach: {data: roomsView, afterRender: afterRender } -->
      <div class="float">
        <!-- ko template: {name: 'room-template', foreach: $data} -->
        <!-- /ko -->
      </div>
      <!-- /ko -->
    </div>

  </div>
</script>
<script type="text/html" id="room-template">
  <div class="number-hotel">
    <a href="#" class="del-hotel" data-bind="click:removeRoom">удалить</a>
    <h5>Номер <span data-bind="text: index + 1"></span></h5>
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
        до 12 лет с местом
      </div>
      <div class="small-childs">
        <div class="inputDIV">
            <input type="text" name="adult3" data-bind="css: {active: infants() > 0}, value: infants" class="">
            <a href="#" class="plusOne" data-bind="click: plusOne" rel="infants" style="display: none;">+</a>
            <a href="#" class="minusOne" data-bind="click: minusOne" rel="infants" style="display: none;">-</a>
        </div>
        до 2 лет без места
      </div>
    </div>
    <div class="one-str" data-bind="foreach: ages, visible: ages().length">
      <div class="ages">
        <input data-bind="value: age">
        лет
      </div>
    </div>
    <a href="#" data-bind="click: addRoom, visible: last() && index<3" class="addOtherRoom"><span class="ico-plus"></span>Добавить еще один номер.</a>
  </div>
</script>
