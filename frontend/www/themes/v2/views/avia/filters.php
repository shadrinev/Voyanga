<?php
    $images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
?>
<script type="text/html" id="avia-filters">
    <div class="filter-content">
        <div class="slide-filter">
            <div>
                <select id='aviaFlightClass' class="selectSlider" data-bind="value: results.serviceClassFilter"><option value="B">Бизнес</option><option value="A" selected="selected">Эконом</option></select>
            </div>
        </div>

        <div class="div-filter">

            <div class="slider-filter">
                <div>
                    <select id='aviaOnlyDirectFlights' class="selectSlider" data-bind="value: results.onlyDirectFilter"><option value="0" selected="selected">Все рейсы</option><option value="1">Прямые</option></select>
                </div>
            </div>

            <input type="checkbox" name="aviaShortTransits" id="aviaShortTransits" data-bind="checked: results.onlyShortFilter"> <label for="aviaShortTransits">Только короткие пересадки</label>

        </div>
        <div class="div-filter">
            <div class="slider-filter" style="text-align:center; margin-bottom:18px;">
                <div style="width: 200px; margin-left: 0px;">
                    <select id='aviaShowReturnFilters' class="selectSlider"><option value="0" selected="selected">Туда</option><option value="1">Обратно</option></select>
                </div>
                <br>
                <br>
            </div>
            <h4>Время вылета</h4>

            <div class="slide-filter">
                <br>
                <div class="slider-wrapper-div">
                    <input id="departureTimeSliderDirect" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.departureFromToTimeActive"/>
                </div>
                <div class="slider-wrapper-div">
                    <input id="departureTimeSliderReturn" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.departureFromToTimeReturnActive"/>
                </div>
            </div>
            <h4>Время прилета</h4>

            <div class="slide-filter">
                <br />
                <div class="slider-wrapper-div">
                    <input id="arrivalTimeSliderDirect" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.arrivalFromToTimeActive"/>
                </div>
                <div class="slider-wrapper-div">
                    <input id="arrivalTimeSliderReturn" type="slider" name="departureTimeSlider" value="480;1020" data-bind="value: results.timeLimits.arrivalFromToTimeReturnActive"/>
                </div>
            </div>

            </div>


        <div class="div-filter">
            <h4><div data-bind="text: results.departureCity" style="display: inline-block"></div> <a href="#" class="clean" data-bind="click: results.resetDepartureAirports">Очистить</a></h4>
            <div id="more-departureAirports" class="more-filters">
                <ul data-bind="foreach: results.departureAirports">
                    <li><input type="checkbox" data-bind="checked: active,attr:{id: 'apd-'+$index()}"> <label data-bind="text: name,attr:{for: 'apd-'+$index()}">Шереметьево</label></li>
                </ul>
            </div>
            <!-- ko if: results.departureAirports.length > 3 -->
            <div class="all-list">
                <a href="#" onclick="return AviaFilters.showMoreDiv(this,'more-departureAirports')">Все аэропорты</a>
            </div>
            <!-- /ko -->
        </div>
        <div class="div-filter">
            <h4><div data-bind="text: results.arrivalCity" style="display: inline-block"></div> <a href="#" class="clean" data-bind="click: results.resetArrivalAirports">Очистить</a></h4>
            <div id="more-arrivalAirports" class="more-filters">
                <ul data-bind="foreach: results.arrivalAirports">
                    <li><input type="checkbox" data-bind="checked: active,attr:{id: 'apa-'+$index()}"> <label data-bind="text: name,attr:{for: 'apa-'+$index()}">Шереметьево</label></li>
                </ul>
            </div>
            <!-- ko if: results.arrivalAirports.length > 3 -->
            <div class="all-list">
                <a href="#" onclick="return AviaFilters.showMoreDiv(this,'more-arrivalAirports')">Все аэропорты</a>
            </div>
            <!-- /ko -->
        </div>
        <div class="div-filter">
            <h4>Авиакомпании <a href="#" class="clean" data-bind="click: results.resetAirlines">Очистить</a></h4>
            <div id="more-airlines" class="more-filters">
                <ul data-bind="foreach: results.airlines">
                    <li><input type="checkbox" data-bind="checked: active,attr:{id: 'aline-'+name}"> <label data-bind="text: visibleName,attr:{for: 'aline-'+name}">Аэрофлот</label></li>
                </ul>
            </div>
            <div class="all-list">
                <a href="#" onclick="return AviaFilters.showMoreDiv(this,'more-airlines')">Все авиакомпании</a>
            </div>
            </div>
        </div>
</script>
