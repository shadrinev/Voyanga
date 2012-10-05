<script type="text/html" id="tour-panel-template">
    <div class="deleteTab"></div>
    <div class="panel">
        <table class="constructorTable">
            <tr>
                <td class="tdCity">
                    <!-- ko if: ($root.tourPanelSet.isFirst()) -->
                    <div class="cityStart">
                        <div class="to">
                            Старт из:
                            <a href="#"><span data-bind="click: showFromCityInput, text: $root.tourPanelSet.startCityReadableGen">Санкт-Петербург</span></a>
                        </div>
                        <div class="startInputTo">
                            <input type="text" tabindex="-1" class="input-path" data-bind="blur: hideFromCityInput">
                            <input type="text" placeholder="Санкт-Петербург" class="second-path" data-bind="blur: hideFromCityInput, autocomplete: {source:'city', iata: $root.tourPanelSet.startCity, readable: $root.tourPanelSet.startCityReadable, readableAcc: $root.tourPanelSet.startCityReadableAcc, readableGen: $root.tourPanelSet.startCityReadableGen}">
                        </div>
                    </div>
                    <!-- /ko -->
                    <div class="from active">
                        <input type="text" tabindex="-1" class="input-path">
                        <input type="text" placeholder="Куда едем?" class="second-path" data-bind="click: hideFromCityInput, autocomplete: {source:'city', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}">
                        <div class="date">
                            <span class="f17">14</span>
                            <br>
                            мая
                        </div>
                        <div class="date">
                            <span class="f17">15</span>
                            <br>
                            мая
                        </div>
                    </div>
                    <a href="#" class="add-tour"></a>
                </td>
                <td class="tdPeople">
                    <span data-bind="template: {name: rooms()[0].template, data: rooms}"></span>
                </td>
                <td class="tdButton">
                    <div class="btn-find"></div>
                </td>
            </tr>
        </table>
    </div>
</script>