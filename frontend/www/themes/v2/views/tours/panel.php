<script type="text/html" id="tour-panel-template">
    <table class="panelTable">
        <tbody><tr>
            <td class="contTD">
                <div class="cityStart">
                    <div class="to">
                        Старт из:
                        <a href="#"><span>Санкт-Петербург11</span></a>
                    </div>
                    <div class="input-to">
                        <input type="text" placeholder="Амстердам" class="input-path" style="width:240px;">
                    </div>
                </div>
                <div class="data active">
                    <input type="text" placeholder="Амстердам" class="input-path" data-bind="autocomplete: {source:'city', iata: startCity, readable: startCityReadable, readableAcc: startCityReadableAcc, readableGen: startCityReadableGen}">
                    <div class="date">
                        <span class="f17">12</span>
                        <br>
                        мая
                    </div>
                    <div class="date">
                        <span class="f26"></span>
                    </div>
                </div>
            </td>
            <td class="btnTD">
            </td>
        </tr>
        </tbody></table>
</script>
