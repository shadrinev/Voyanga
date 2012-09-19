<?php
    $images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
?>
<script type="text/html" id="avia-filters">
  <div class="filter-content">
    <div class="slide-filter">
      <div>
        <select data-bind="slider:true, value: serviceClass.selection"><option value="B">Бизнес</option><option value="A" selected="selected">Эконом</option></select>
      </div>
    </div>
    <div class="div-filter">
      <div class="slider-filter">
        <div>
          <select data-bind="slider: true, value: onlyDirect.selection"><option value="0" selected="selected">Все рейсы</option><option value="1">Прямые</option></select>
        </div>
      </div>
      <input type="checkbox" data-bind="checked: shortStopover.selection"> <label>Только короткие пересадки</label>
    </div>
    <div class="div-filter">
      <div class="slider-filter smallSlide" style="text-align:center; margin-bottom:14px;" data-bind="visible: rt">
        <div>
          <select class="smallSlider" data-bind="slider: true, value: showRt"><option value="0" selected="selected">Туда</option><option value="1">Обратно</option></select>
        </div>
        <br>
        <br>
      </div>
      <h4>Время вылета <span class="flightDirectionName" data-bind="visible: rt, text:showRtText">туда</span></h4>
      <div class="slide-filter">
        <div class="slider-wrapper-div" data-bind="visible: !(+showRt())">
          <input data-bind="timeSlider: departure"/>
        </div>
	<!-- FIXME -->
        <div class="slider-wrapper-div" data-bind="if: rt, visible: +showRt()">
          <input data-bind="timeSlider: rtDeparture"/>
        </div>
      </div>
      <h4 style="margin-top:25px">Время прилета <span class="flightDirectionName" data-bind="visible: rt, text:showRtText">туда</span></h4>
      <div class="slide-filter">
        <div class="slider-wrapper-div" data-bind="visible: !(+showRt())">
          <input data-bind="timeSlider: arrival "/>
        </div>
	<!-- FIXME -->
        <div class="slider-wrapper-div" data-bind="if: rt, visible: +showRt()">
          <input data-bind="timeSlider: rtArrival"/>
        </div>
      </div>
    </div>
<!-- AIRPORTS -->
    <div class="div-filter" data-bind="template: {name: 'avia-filter-list', data: departureAirport, if: departureAirport.active}, visible: departureAirport.active">
    </div>
    <div class="div-filter" data-bind="template: {name: 'avia-filter-list', data: arrivalAirport, if: arrivalAirport.active}, visible: arrivalAirport.active">
    </div>
    <div class="div-filter" data-bind="template: {name: 'avia-filter-list', data: airline, if: airline.active}, visible: airline.active">
    </div>
  </div>
</script>
<script type="text/html" id="avia-filter-list">
      <h4><div data-bind="text: caption" style="display: inline-block"></div> <a href="#" class="clean" data-bind="click: reset">Очистить</a></h4>
      <ul data-bind="foreach: options">
        <!-- ko if: $index() < 3 -->
        <li><input type="checkbox" data-bind="checked: checked"> <label data-bind="text: key">Шереметьево</label></li>
        <!-- /ko -->
      </ul>
      <!-- ko if: options().length > 3 -->
      <div class="more-filters">
        <ul data-bind="foreach: options">
          <!-- ko if: $index() >= 3 -->
          <li><input type="checkbox" data-bind="checked: checked"> <label data-bind="text: key">Шереметьево</label></li>
          <!-- /ko -->
        </ul>
      </div>
      <div class="all-list">
        <a href="#" data-bind="click: showMore, text: moreLabel">Все аэропорты</a>
      </div>
      <!-- /ko -->
    </div>
</script>
<script type="text/html">
<div>
    <div class="div-filter" data-bind="visible: results.airlines.length>1">
      <h4>Авиакомпании <a href="#" class="clean" data-bind="click: ">Очистить</a></h4>
      <ul data-bind="foreach: results.airlines">
        <!-- ko if: $index() < 3 -->
        <li><input type="checkbox" data-bind="checked: active,attr:{id: 'aline-'+name}"> <label data-bind="text: visibleName,attr:{for: 'aline-'+name}">Аэрофлот</label></li>
        <!-- /ko -->
      </ul>
      <!-- ko if: results.airlines.length > 3 -->
      <div id="more-airlines" class="more-filters">
        <ul data-bind="foreach: results.airlines">
          <!-- ko if: $index() >= 3 -->
          <li><input type="checkbox" data-bind="checked: active,attr:{id: 'aline-'+name}"> <label data-bind="text: visibleName,attr:{for: 'aline-'+name}">Аэрофлот</label></li>
          <!-- /ko -->
        </ul>
      </div>
      <div class="all-list">
        <a href="#" onclick="return AviaFilters.showMoreDiv(this,'more-airlines')">Все авиакомпании</a>
      </div>
      <!-- /ko -->
    </div>
  </div>
</script>
