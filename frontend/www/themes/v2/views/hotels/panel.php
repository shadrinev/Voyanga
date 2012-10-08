<script type="text/html" id="hotels-panel-template">
  <table class="hotelTable">
    <tr>
      <td class="tdCity">
        <div class="data">
            <input class="input-path" tabindex="-1" type="text" data-bind="autocomplete: {source:'city/hotel_req/1', iata: city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}">
            <input class="second-path" type="text" placeholder="Город" data-bind="autocomplete: {source:'city/hotel_req/1', iata: city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}">
        </div>
      </td>
      <td class="tdPeople">
        <span data-bind="template: {name: rooms()[0].template, data: rooms}"></span>
      </td>
      <td class="btnTD">
        <a class="btn-find" data-bind="click: navigateToNewSearch, visible: formFilled">Найти</a>
      </td>
    </tr>
  </table>
</script>