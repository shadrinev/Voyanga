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
                                <input type="text" placeholder="Санкт-Петербург" class="second-path" data-bind="blur: hideFromCityInput, autocomplete: {source:'city/airport_req/1', iata: $parent.startCity, readable: $parent.startCityReadable, readableAcc: $parent.startCityReadableAcc, readableGen: $parent.startCityReadableGen}">
                            </div>
                            <!-- /ko -->
                        </div>
                        <div class="from" data-bind="css: {active: checkIn()}">
                            <input type="text" tabindex="-1" class="input-path">
                            <input type="text" placeholder="Куда едем?" class="second-path" data-bind="hasfocus: hasfocus, click: hideFromCityInput, autocomplete: {source:'city/airport_req/1', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}, css: {isFirst: $parent.isFirst()}">
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
                        <div class="btn-find" data-bind="click: navigateToNewSearch, visible: formFilled"></div>
                    </td>
                    <!-- /ko -->
                </tr>
            </table>
        </div>
    <!-- /ko -->
</script>

<script id="tours-panel-template" type="text/html">
  <div class="btn-timeline-and-condition">
    <a href="#" class="btn-timeline" data-bind="click: timeline.showTimeline, css: {active: !timeline.termsActive}">Таймлайн</a>
    <a href="#" class="btn-condition" data-bind="click: timeline.showConditions, css: {active: timeline.termsActive}">Условия</a>
  </div>
  
  <div class="slide-tmblr">
    <div class="timeline" data-bind="style: {top: timeline.termsActive?'-68px':0}">
      <div class="btn-left" data-bind="click: timeline.scrollLeft"> </div>
      <div class="btn-right" data-bind="click: timeline.scrollRight"></div>
      <div class="timedatelineOverflow">
	<ul class="timedateline"  data-bind="foreach: timeline.data, style: {width: timeline.data().length * 32 + 'px', marginLeft: '-' + timeline.timelinePosition() + 'px'}">
	  <li>
	    <!-- ko if: avia -->
	    <div class="trip-fly"></div>
	    <!-- /ko -->
	    <!-- ko if:hotel -->
	    <div class="trip-hotel" style="left:16px;" data-bind="style: {width:'' + hotel.duration * 32 + 'px'}"></div>
	    <!-- /ko -->
	    <div class="date" data-bind="text: day"></div>
	  </li>
	  <!-- li>
	    <div class="trip-hotel" style="left:18px; width:188px;"></div>
	    <div class="date">26</div>
	  </li -->
	</ul>
      </div>
      <div class="left-corners"></div>
      <div class="right-corners"></div>
    </div>
    

    <div class="condition"
         data-bind="template: { name: original_template, data: $data, afterRender: afterRender },style: {top: timeline.termsActive?'-15px':'68px'}">
    </div>
  </div>
  
  <div class="clear"></div>
  <!-- BTN MINIMIZE -->
  <!-- fixme -->
</div>
</script>
