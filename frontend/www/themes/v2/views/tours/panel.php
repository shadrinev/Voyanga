<script type="text/html" id="tour-panel-template">
    <!-- ko template: {foreach: $data.panels, afterRender: $data.afterRender} -->
    <div class="deleteTab" data-bind="click: $parent.deletePanel"></div>
    <div class="panel">
        <table class="panelTable constructorTable">
            <tr>

                <td class="tdCityStart">
                    <div class="cityStart">
                        <!-- ko if: ($parent.isFirst()) -->
                        <div class="to">
                            Старт из:
                            <a href="#"><span data-bind="click: showFromCityInput, text: $parent.startCityReadableGen">Санкт-Петербург</span></a>
                        </div>
                        <div class="startInputTo">
                            <div class="bgInput">
                                <div class="left"></div>
                                <div class="center"></div>
                                <div class="right"></div>
                            </div>
                            <input type="text" tabindex="-1" class="input-path" data-bind="blur: hideFromCityInput">
                            <input type="text" placeholder="Санкт-Петербург" class="second-path"
                                   data-bind="blur: hideFromCityInput, autocomplete: {source:'city/airport_req/1', iata: $parent.startCity, readable: $parent.startCityReadable, readableAcc: $parent.startCityReadableAcc, readableGen: $parent.startCityReadableGen}">
                        </div>
                        <!-- /ko -->
                    </div>
                </td>
                <td class="tdCity">
                    <div class="data">
                        <div class="from" data-bind="css: {active: checkIn()}">
                            <div class="bgInput">
                                <div class="left"></div>
                                <div class="center"></div>
                                <div class="right"></div>
                            </div>
                            <input type="text" tabindex="-1" class="input-path">
                            <input type="text" placeholder="Куда едем?" class="second-path"
                                   data-bind="hasfocus: hasfocus, autocomplete: {source:'city/airport_req/1', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}, css: {isFirst: $parent.isFirst()}">

                            <div class="date"
                                 data-bind="click: showCalendar, html:checkInHtml(), css: {'noDate': !checkIn()}">
                            </div>
                            <div class="date"
                                 data-bind="click: showCalendar, html:checkOutHtml(), css: {'noDate': !checkOut()}">
                            </div>
                        </div>
                    </div>
                </td>
                <td class="tdAddTour">
                    <!-- ko if: ($data.isLast) -->
                    <a href="#" class="add-tour"
                       data-bind="click: $parent.addPanel, visible: !$parent.isMaxReached()"></a>
                    <!-- /ko -->
                </td>
                <td class="tdPeople">
                    <!-- ko if: ($data.isLast) -->
                    <span
                        data-bind="template: {name: $data.peopleSelectorVM.template, data: $data.peopleSelectorVM}"></span>
                    <!-- /ko -->
                </td>
                <td class="tdButton">
                    <!-- ko if: ($data.isLast) -->
                    <div class="btn-find"
                         data-bind="click: $parent.navigateToNewSearch, css: {inactive: $parent.formNotFilled}"></div>
                    <!-- /ko -->
                </td>
            </tr>
        </table>
    </div>
    <!-- /ko -->
</script>

<script id="tours-panel-template" type="text/html">
<div class="toursPanel">
  <div class="btn-timeline-and-condition" data-bind="visible: !onlyTimeline">
    <a href="#" class="btn-timeline" data-bind="click: timeline.showTimeline, css: {active: !timeline.termsActive}">Таймлайн</a>
    <a href="#" class="btn-condition" data-bind="click: timeline.showConditions, css: {active: timeline.termsActive}">Условия</a>
  </div>
  
  <div class="slide-tmblr" data-bind="style: {overflow: timeline.termsActive?'visible':'hidden'}">
     <div class="divTimeline" data-bind="style: {top: timeline.termsActive?'-64px':0}">
        <div class="timeline">
          <div class="alphaLeft"></div><div class="alphaRight"></div>
          <div class="btn-left" data-bind="click: timeline.scrollLeft"> </div>
          <div class="btn-right" data-bind="click: timeline.scrollRight"></div>
          <div class="timedatelineOverflow">
            <ul class="timedateline"  data-bind="foreach: timeline.data, style: {width: timeline.data().length * 32 + 'px', marginLeft: '-' + timeline.timelinePosition() + 'px'}">
              <li>
                <!-- ko if: avia -->
                   <div data-bind="css:{icoFlyTimeline:first}"></div><div class="trip-fly" data-bind="click: $parent.setActiveTimelineAvia, css:{active: avia.item==$parent.selection()}"></div>
                <!-- /ko -->
                <!-- ko if:hotel -->
                <div data-bind="css:{icoHotelTimeline:first}"></div><div class="trip-hotel" style="left:16px;" data-bind="style: {width:'' + hotel.duration * 32 + 'px'}, click: $parent.setActiveTimelineHotels, css:{active: hotel.item==$parent.selection()}"></div>
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
     </div>
    <!-- ko if: !onlyTimeline -->
     <div class="divCondition" data-bind="style: {top: timeline.termsActive?'0px':'68px'}">
        <div class="condition"
             data-bind="template: {name: original_template, data: $data, afterRender: afterRender }">
        </div>
     </div>
    <!-- /ko -->
  </div>
  
  <div class="clear"></div>
  <!-- BTN MINIMIZE -->
  <!-- fixme -->
</div>
</div>
</script>
