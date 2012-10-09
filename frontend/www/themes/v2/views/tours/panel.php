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
                        <div class="from" data-bind="css: {active: city()}">
                            <input type="text" tabindex="-1" class="input-path">
                            <input type="text" placeholder="Куда едем?" class="second-path" data-bind="click: hideFromCityInput, autocomplete: {source:'city', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}, css: {isFirst: $parent.isFirst()}">
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
    <a href="#" class="btn-timeline active">Таймлайн</a>
    <a href="#" class="btn-condition">Условия</a>
  </div>
  
  <div class="slide-tmblr">
    <div class="timeline">
      <div class="btn-left"></div>
      <div class="btn-right"></div>
      <div class="timedatelineOverflow">
	<ul class="timedateline" style="width:840px;">
	  <li>
	    <div class="date">14</div>
	  </li>
	  <li>
	    <div class="date">15</div>
	  </li>
	  <li>
	    <div class="trip-fly"></div>
	    <div class="trip-hotel" style="left:18px; width:312px;"></div>
	    <div class="date">16</div>
	  </li>
	  <li>							
	    <div class="date">17</div>
	  </li>
	  <li>
	    <div class="date">18</div>
	  </li>
	  <li>
	    <div class="date">19</div>
	  </li>
	  <li>
	    <div class="date">20</div>
	  </li>
	  <li>
	    <div class="date">21</div>
	  </li>
	  <li>
	    <div class="date">22</div>
	  </li>
	  <li>
	    <div class="date">23</div>
	  </li>
	  <li>
	    <div class="date">24</div>
	  </li>
	  <li>
	    <div class="date">25</div>
	  </li>
	  <li>
	    <div class="trip-fly"></div>
	    <div class="trip-hotel" style="left:18px; width:188px;"></div>
	    <div class="date">26</div>
	  </li>
	  <li>
	    <div class="date">27</div>
	  </li>
	  <li>
	    <div class="date">28</div>
	  </li>
	  <li>
	    <div class="date">29</div>
	  </li>
	  <li>
	    <div class="date">30</div>
	  </li>
	  <li>
	    <div class="date">31</div>
	  </li>
	  <li>
	    <div class="trip-fly"></div>
	    <div class="date">1</div>
	  </li>
	  <li>
	    <div class="date">2</div>
	  </li>
	  <li>
	    <div class="date">3</div>
	  </li>
	  <li>
	    <div class="date">4</div>
	  </li>
	  <li>
	    <div class="date">5</div>
	  </li>
	  <li>
	    <div class="date">6</div>
	  </li>
	  <li>
	    <div class="date">7</div>
	  </li>
	  <li>
	    <div class="date">8</div>
	  </li>
	  <li>
	    <div class="date">9</div>
	  </li>
	</ul>
      </div>
      <div class="left-corners"></div>
      <div class="right-corners"></div>
    </div>
    

    <div class="condition" style="top: 68px;"
         data-bind="template: { name: original_template, data: $data, afterRender: afterRender }">
    </div>
  </div>
  
  <div class="clear"></div>
  <!-- BTN MINIMIZE -->
  <!-- fixme -->
  <a href="#" class="btn-minimizePanel"><span></span> свернуть</a>
  
  <div class="minimize-rcomended">
    <a href="#" class="btn-minimizeRecomended"> вернуть рекомендации</a>
  </div>
</div>
</script>
