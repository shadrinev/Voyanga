<script type="text/html" id="passengers-template">
    <div class="how-many-man" data-bind="click: show">
        <div class="wrapDivContent">
            <div class="content">
                <!-- ko if: overall()>5 -->
                <!-- ko if: adults()>0 -->
                <div class="man"></div>
                <div class="count"><span>x</span><i data-bind="text: adults()"></i></div>
                <!-- /ko -->
                <!-- ko if: (sum_children())>0 -->
                <div class="child"></div>
                <div class="count"><span>x</span><i data-bind="text: sum_children()"></i></div>
                <!-- /ko -->
                <!-- /ko -->
                <!-- ko if: overall()<=5 -->
                <div class="man" data-bind="repeat: adults"></div>
                <div class="child" data-bind="repeat: sum_children"></div>
                <!-- /ko -->
            </div>
            <div class="btn"></div>
        </div>
    <div class="popup" data-bind="click: preventShow">
        <div class="adults">
            <div class="inputDIV">
                <input type="text" name="adult" data-bind="css: {active: adults() > 0}, value: adults">
                <a href="#" class="plusOne" data-bind="click: plusOne" rel="adults">+</a>
                <a href="#" class="minusOne" data-bind="click: minusOne" rel="adults">-</a>
            </div>
            взрослых
        </div>
        <div class="childs">
            <div class="inputDIV">
                <input type="text" name="adult2" data-bind="css: {active: children() > 0}, value: children">
                <a href="#" class="plusOne" data-bind="click: plusOne" rel="children">+</a>
                <a href="#" class="minusOne" data-bind="click: minusOne" rel="children">-</a>
            </div>
            детей до 12 лет
        </div>
        <div class="small-childs">
            <div class="inputDIV">
                <input type="text" name="adult3" data-bind="css: {active: infants() > 0}, value: infants">
                <a href="#" class="plusOne" data-bind="click: plusOne" rel="infants">+</a>
                <a href="#" class="minusOne" data-bind="click: minusOne" rel="infants">-</a>
            </div>
            детей до 2 лет
        </div>

    </div>
</div>
</script>