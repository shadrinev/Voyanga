<script type="text-html" id="hotels-results">
<h1>Выберите отель в Амстердам, 19-26 мая</h1>
<div class="ticket-content hotels">
    <h2>Найдено отелей: 43</h2>
    <div class="clear"></div>
        <!-- ko foreach: results -->
        <div class="hotels-tickets">
            <div class="content">
                <div class="full-info">
                    <div class="preview-photo">
                        <ul>
                            <li><a href="images/hostel/orig/19959_79_b.jpg" class="photo"><img src="images/hostel/19959_79_b.jpg"></a></li>
                        </ul>
                        <div class="how-much">
                            <a href="#">Фотографий (11)</a>
                        </div>
                    </div>
                    <div class="description">
                    <div class="title">
                        <h2><span data-bind="text:hotelName">Рэдиссон Соня Отель</span> <span class="gradient"></span></h2>
                        <div data-bind="attr: {class: 'stars ' + stars}"></div>
                    </div>
                    <div class="place">
                        <div class="street">
                            <span data-bind="text:address">Санкт-Петребург. ул. Морская Набережная, 31/2</span>
                            <span class="gradient"></span>
                        </div>
                        <a href="#" class="in-the-map"><span class="ico-see-map"></span> <span class="link">На карте</span></a>
                    </div>
                    <div class="text" data-bind="text:description">
                        Этот 4-звездочный отель расположен рядом с площадью Победы и парком Городов-Героев. К услугам гостей большой крытый бассейн и номера с телевизорами с плоским экраном...
                    </div>
                </div>
                <div class="choose-a-hotel">
                    <div class="rating">
                        <span class="value" data-bind="text: rating"></span>
                        <span class="text">рейтинг<br>отеля</span>
                    </div>
                    <a href="#" class="btn-cost"><span class="l"></span><span class="text">Выбрать отель</span></a>
                    <a class="details" href="#" id="popuphotel">Подробнее об отеле</a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="details">
                <ul data-bind="foreach: roomSets">
                    <li class="not-show">
                        <div class="items">
                            <div class="float" data-bind="foreach: rooms">
                                <span class="text" data-bind="text: name">Стандартный двухместный номер</span>
                                <!-- ko if: hasMeal -->
                                 <span class="ico-breakfast"></span> <span data-bind="text:meal">Завтрак</span>
                                <!-- /ko -->
                                <br>
                            </div>
                            <div class="how-cost">
                                <span class="cost" data-bind="text: price">14 200</span><span class="rur f21">o</span> / ночь <br> <span class="grey em" data-bind="visible: rooms.length == 2">За оба номера</span>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </li>
                </ul>
                <div class="tab-ul" data-bind="visible: roomSets.length > 2">
                    <a href="#">Посмотреть все результаты</a>
                </div>
                <span class="lv"></span>
                <span class="rv"></span>
            </div>
        </div>
        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="bh"></span>
    </div>
    <!-- /ko -->
</div>
</script>
<script type="text/html" id="hotels-panel-template">
<div class="path">
    <table class="hotel-table">
        <tbody><tr>
            <td class="td-input-hotel">
                <div class="data">
                    <input class="input-path-hotel" type="text" placeholder="Город">
                </div>
                <div class="how-many-man hotel">
                <!-- ko foreach: rooms -->
                    <!-- ko foreach: $data -->
                        <div class="content">
                            <span class="num" data-bind="text: $index() + 1"></span>
                            <div class="man" data-bind="repeat: adults"></div>
                            <div class="child" data-bind="repeat: children"></div>
                        </div>
                    <!-- /ko -->
                <!-- /ko -->
                <div class="btn"></div>
                    <div class="popup">
                    <!-- ko foreach: rooms -->
                        <div class="float">
                        <!-- ko foreach: $data -->
                            <!-- ko template: {name: 'room-template', data: $data} -->
                            <!-- /ko -->
                        <!-- /ko -->
                        </div>
                    <!-- /ko -->
                    </div>
                </div>
            </td>
            <td>
                <a class="btn-find">Найти</a>
            </td>
        </tr>
    </tbody></table>
</div>
</script>
<script type="text/html" id="room-template">
    <div class="number-hotel">
    <h5>Номер <span data-bind="text: $index() + 1"></span></h5>
        <div class="one-str">
            <div class="adults">
                <div class="inputDIV">
                    <input type="text"  data-bind="value: adults" name="adult" class="active">
                    <a href="#" class="plusOne">+</a>
                    <a href="#" class="minusOne">-</a>
                </div>
                взрослых
            </div>
            <div class="childs">
                <div class="inputDIV">
                    <input type="text" data-bind="value: children" name="adult2">
                    <a href="#" class="plusOne">+</a>
                    <a href="#" class="minusOne">-</a>
                </div>
                детей от 12 до 18 лет
            </div>
        </div>
        <!-- ko if: children > 0 -->
            <div class="one-str" data-bind="foreach: ages">
                <div class="ages">
                    <input data-bind="value: $data, attr:{name: 'asd'+$index()}" >
                    лет
                </div>
            </div>
        <!-- /ko -->
    </div>
</script>
<script id="hotels-index" type="text/html">
<h1> Hello, hotels index</h1>
</script>
