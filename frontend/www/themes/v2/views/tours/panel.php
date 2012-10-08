<script type="text/html" id="tour-panel-template">
    <div class="deleteTab" data-bind="click: $root.tourPanelSet.deletePanel"></div>
    <div class="panel">
        <table class="constructorTable">
            <tr>
                <td class="tdCity">
                    <div class="cityStart">
                        <!-- ko if: ($root.tourPanelSet.isFirst()) -->
                        <div class="to">
                            Старт из:
                            <a href="#"><span data-bind="click: showFromCityInput, text: $root.tourPanelSet.startCityReadableGen">Санкт-Петербург</span></a>
                        </div>
                        <div class="startInputTo">
                            <input type="text" tabindex="-1" class="input-path" data-bind="blur: hideFromCityInput">
                            <input type="text" placeholder="Санкт-Петербург" class="second-path" data-bind="blur: hideFromCityInput, autocomplete: {source:'city', iata: $root.tourPanelSet.startCity, readable: $root.tourPanelSet.startCityReadable, readableAcc: $root.tourPanelSet.startCityReadableAcc, readableGen: $root.tourPanelSet.startCityReadableGen}">
                        </div>
                        <!-- /ko -->
                    </div>
                    <div class="from active">
                        <input type="text" tabindex="-1" class="input-path">
                        <input type="text" placeholder="Куда едем?" class="second-path" data-bind="click: hideFromCityInput, autocomplete: {source:'city', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}">
                        <div class="date" data-bind="click: showCalendar">
                            <span class="f17">?</span>
                            <br>
                            <span></span>
                        </div>
                        <div class="date" data-bind="click: showCalendar">
                            <span class="f17">?</span>
                            <br>
                            <span></span>
                        </div>
                    </div>
                    <!-- ko if: ($data.isLast) -->
                            <a href="#" class="add-tour" data-bind="click: $root.tourPanelSet.addPanel, visible: !$root.tourPanelSet.isMaxReached()"></a>
                    <!-- /ko -->
                </td>
                <!-- ko if: ($data.isLast) -->
                <td class="tdPeople">
                    <span data-bind="template: {name: rooms()[0].template, data: rooms}"></span>
                </td>
                <td class="tdButton">
                    <div class="btn-find"></div>
                </td>
                <!-- /ko -->
            </tr>
        </table>
    </div>
</script>