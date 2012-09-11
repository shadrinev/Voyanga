<script id="avia-popup-flight" type="text/html">
        <div data-bind="css: {'start-path': $index()==0, 'end-path': $index()==($length()-1)}">
            <div class="information">
                <div class="start-fly" data-bind="css: {'no-way': $index()!=0}">
                    <div class="time" data-bind="text: departureTime()">
                        9:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city" data-bind="text: departureCity">Санкт-Петербург,</span>,
                        <span class="airport" data-bind="text: departureAirport">Пулково-2</span>
                    </div>
                </div>
                <div class="time-fly">
                    <div class="icon wait"></div>
                    <div class="info">
                        Перелет продлится <span data-bind="text: duration()">1 ч. 50 м.<span>
                    </div>
                </div>
                <div class="finish-fly" data-bind="css: {'no-way': $index()!=($length()-1)}">
                    <div class="time" data-bind="text: arrivalTime()">
                        9:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city" data-bind="text: arrivalCity">Санкт-Петербург</span>,
                        <span class="airport" data-bind="text: arrivalAirport">Пулково-2</span>
                    </div>
                </div>
            </div>
            <div class="aviacompany">
                <img data-bind="attr: {'src': '/img/airlines/' + transportAirline +'.png'}" >
                <span data-bind="text: flightCode"></span>
            </div>
        </div>
        <!-- ko if: $index() < ($length() - 1) -->
        <div class="transitum">
            Пересадка: между рейсами  ХЗ СКОЛЬКО 1 ч. 30 м.
        </div>
        <!-- /ko -->
</script>
<!-- FIXME -->
<script id="popup-departure-choices" type="text/html">
                    <li data-bind="css: {active: hash() == $parent.hash()}, click: $parent.chooseStacked">
                        <!-- FIXME Why this is radio? -->
                        <input type="radio" name="radio01" id="name01" checked="checked">
                        <label for="name01"><span data-bind="text:departureTime()">06:10</span></label>
                    </li>
</script>
<script id="popup-departure-choices-rt" type="text/html">
                    <li data-bind="css: {active: hash() == $parent.rtHash()}, click: $parent.chooseRtStacked">
                        <!-- FIXME Why this is radio? -->
                        <input type="radio" name="radio01" id="name01" checked="checked">
                        <label for="name01"><span data-bind="text:departureTime()">06:10</span></label>
                    </li>
</script>
