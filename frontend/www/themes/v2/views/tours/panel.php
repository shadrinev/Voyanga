<script type="text/html" id="tour-panel-template">
    <!-- ko foreach: $data.panels -->
        <div class="deleteTab" data-bind="click: $parent.deletePanel"></div>
        <div class="panel">
            <table class="constructorTable">
                <tr>
                    <td class="tdCity">
                        <div class="cityStart">
                            <!-- ko if: ($parent.isFirst()) -->
                            <div class="to">
                                Старт из:
                                <a href="#"><span data-bind="click: showFromCityInput, text: $parent.startCityReadableGen">Санкт-Петербург</span></a>
                            </div>
                            <div class="startInputTo">
                                <input type="text" tabindex="-1" class="input-path" data-bind="blur: hideFromCityInput">
                                <input type="text" placeholder="Санкт-Петербург" class="second-path" data-bind="blur: hideFromCityInput, autocomplete: {source:'city', iata: $parent.startCity, readable: $parent.startCityReadable, readableAcc: $parent.startCityReadableAcc, readableGen: $parent.startCityReadableGen}">
                            </div>
                            <!-- /ko -->
                        </div>
                        <div class="from active">
                            <input type="text" tabindex="-1" class="input-path">
                            <input type="text" placeholder="Куда едем?" class="second-path" data-bind="click: hideFromCityInput, autocomplete: {source:'city', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}">
                            <div class="date" data-bind="click: showCalendar, html:checkInHtml(), css: {'noDate': !checkIn()}">
                            </div>
                            <div class="date" data-bind="click: showCalendar, html:checkOutHtml(), css: {'noDate': !checkOut()}">
                            </div>
                        </div>
                        <!-- ko if: ($data.isLast) -->
                                <a href="#" class="add-tour" data-bind="click: $parent.addPanel, visible: !$parent.isMaxReached()"></a>
                        <!-- /ko -->
                    </td>
                    <!-- ko if: ($data.isLast) -->
                    <td class="tdPeople">
                         <span data-bind="template: {name: $data.peopleSelectorVM.template, data: $data.peopleSelectorVM}"></span>
                    </td>
                    <td class="tdButton">
                        <div class="btn-find"></div>
                    </td>
                    <!-- /ko -->
                </tr>
            </table>
        </div>
    <!-- /ko -->
</script>
