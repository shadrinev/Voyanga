
<script type="text/html" id="avia-filters">
    <div class="filter-content">
        <div class="slide-filter">
            <img src="<?= $images ?>/images/img-filter-slide01.png">
            <select id='aviaFlightClass' class="selectSlider" data-bind="value: results.serviceClassFilter"><option value="B">Бизнес</option><option value="A" selected="selected">Эконом</option></select>
        </div>

        <div class="div-filter">

            <div class="slider-filter">
                <img src="<?= $images ?>/images/img-filter-slide02.png">
                <select id='aviaOnlyDirectFlights' class="selectSlider" data-bind="value: results.onlyDirectFilter"><option value="0" selected="selected">Все рейсы</option><option value="1">Прямые</option></select>
            </div>

            <input type="checkbox" name="aviaShortTransits" id="aviaShortTransits" data-bind="checked: results.onlyShortFilter"> <label for="aviaShortTransits">Только короткие пересадки</label>

        </div>
        <div class="div-filter">
            <div class="slider-filter" style="text-align:center; margin-bottom:18px;">
                <img src="<?= $images ?>/images/tuda.png">
                <br>
                <input type="text" id="megaInput" data-bind="value: testInput">
                <div style="width: 200px; margin-left: 0px;">
                    <select id='aviaShowReturnFilters' class="selectSlider"><option value="0" selected="selected">Туда</option><option value="1">Обратно</option></select>
                </div>
                <br>
                <br>
            </div>
            <h4>Время вылета</h4>

            <div class="slide-filter">
                <br>
                <br>
                <div style="width: 200px; margin-left: 0px;">
                    <input id="departureTimeSliderDirect" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.departureFromToTimeActive"/>
                </div>
                <div style="width: 200px; margin-left: 0px;">
                    <input id="departureTimeSliderReturn" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.departureFromToTimeReturnActive"/>
                </div>
            </div>
            <h4>Время прилета</h4>

            <div class="slide-filter">
                <br />
                <div style="width: 200px; margin-left: 0px;">
                    <input id="arrivalTimeSliderDirect" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.arrivalFromToTimeActive"/>
                </div>
                <div style="width: 200px; margin-left: 0px;">
                    <input id="arrivalTimeSliderReturn" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.arrivalFromToTimeReturnActive"/>
                </div>
            </div>

            </div>

            <div class="div-filter">
                <h4>Москва</h4>
                <ul data-bind="foreach: results.airports">
                <li><input type="checkbox" data-bind="checked: active"> <label for="ch01" data-bind="text: name">Шереметьево</label></li>
                </ul>
                </div>
            <div class="div-filter">
                <h4>Москва <a href="#" class="clean">Очистить</a></h4>
            <ul data-bind="foreach: results.airlines">
                <li><input type="checkbox" data-bind="checked: active"> <label data-bind="text: name">Аэрофлот</label></li>
                </ul>
            <div class="all-list">
                <a href="#">Все авиакомпании</a>
                </div>
                </div>
            </div>
</script>
