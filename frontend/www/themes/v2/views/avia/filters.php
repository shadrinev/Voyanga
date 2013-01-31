<?php
    $images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
?>
<script type="text/html" id="avia-filters">

<div class="filter-content avia">
  
    <div class="slide-filter first">
      <div>
        <select data-bind="slider:true, value: serviceClass.selection"><option value="B">Бизнес</option><option value="A" selected="selected">Эконом</option></select>
      </div>
    </div>
	
<div class="innerFilter">
    
    <div class="scrollBlock" id="scroll-pane"> 
    
	    <div class="div-filter">
	      <div class="slider-filter">
	        <div>
	          <select data-bind="slider: true, value: onlyDirect.selection"><option value="0" selected="selected">Все рейсы</option><option value="1">Прямые</option></select>
	        </div>
	      </div>
	      <input type="checkbox" data-bind="checkbox:{label: 'Только короткие пересадки', checked: shortStopover.selection}" />
	    </div>
	    <div class="div-filter">
	      <h4>Время вылета <span class="flightDirectionName" data-bind="visible: rt">туда</span></h4>
	      <div class="slide-filter">
	        <div class="slider-wrapper-div">
	          <input data-bind="timeSlider: departure"/>
	        </div>
		<!-- FIXME -->
	        <!-- div class="slider-wrapper-div" data-bind="if: rt, visible: +showRt()">
	          <input data-bind="timeSlider: rtDeparture"/>
	        </div -->
	      </div>
              <!-- ko if: rt -->
	      <h4 style="margin-top:25px">Время вылета <span class="flightDirectionName" data-bind="visible: rt">обратно</span></h4>
	      <div class="slide-filter">
	        <div class="slider-wrapper-div">
	          <input data-bind="timeSlider: rtDeparture "/>
	        </div>
	      </div>
              <!-- /ko -->
	    </div>
	<!-- AIRPORTS -->
	    <div class="div-filter" data-bind="template: {'name': 'avia-filter-list', 'data': departureAirport, 'if': departureAirport.active}, 'visible': departureAirport.active">
	    </div>
	    <div class="div-filter" data-bind="template: {'name': 'avia-filter-list', 'data': arrivalAirport, 'if': arrivalAirport.active}, 'visible': arrivalAirport.active">
	    </div>
	    <div class="div-filter" data-bind="template: {'name': 'avia-filter-list', 'data': airline, 'if': airline.active}, 'visible': airline.active">
	    </div>
	    </div>

	</div>
	
</div>
</script>
<script type="text/html" id="avia-filter-list">
      <h4><div data-bind="text: caption" style="display: inline-block"></div> <a href="#" class="clean" data-bind="click: reset, visible: selection().length">Очистить</a></h4>
      <ul data-bind="foreach: options">
        <!-- ko if: $index() < 3 -->
        <li><input type="checkbox" data-bind="checkbox:{'label': key, 'checked': checked}"></li>
        <!-- /ko -->
      </ul>
      <!-- ko if: options().length > 3 -->
      <div class="more-filters">
        <ul data-bind="foreach: options">
          <!-- ko if: $index() >= 3 -->
          <li><input type="checkbox" data-bind="checkbox:{'label': key, 'checked': checked}"></li>
          <!-- /ko -->
        </ul>
      </div>
      <div class="all-list">
        <a href="#" data-bind="click: showMore, text: moreLabel">Все аэропорты</a>
      </div>
      <!-- /ko -->
    </div>
</script>
