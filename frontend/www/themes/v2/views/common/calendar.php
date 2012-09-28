<script type="text/html" id="calendar-template">
    <div class="calendarSlide">
        <div class="innerCalendar">
            <h1 data-bind="text:fakoPanel().calendarText"></h1>
            <a href='#' class="btnCloseCal" data-bind="click:fakoPanel().minimizeCalendar"></a>
        </div>
        <div class="bg-Calendar">
            <div id='voyanga-calendar'>
                <div class="weekDaysVoyanga">
                    <div class="weekDaysVoyangaInner">
                        <div class="weekDayVoyanga"><span>Понедельник</span></div>
                        <div class="weekDayVoyanga"><span>Вторник</span></div>
                        <div class="weekDayVoyanga"><span>Среда</span></div>
                        <div class="weekDayVoyanga"><span>Четверг</span></div>
                        <div class="weekDayVoyanga"><span>Пятница</span></div>
                        <div class="weekDayVoyanga weekEnd"><span>Суббота</span></div>
                        <div class="weekDayVoyanga weekEnd"><span>Воскресенье</span></div>
                    </div>
                </div>
                <div class="calendarUnscrollVoyanga">
                    <div class="calendarGridVoyanga">
                        <div class="calendarDIVVoyanga"></div>
                    </div>
                </div>
                <div class="monthLineVoyanga">
                    <div class="knobVoyanga" id="voyangaCalendarKnob" style="left: 0%;"></div>
                </div>
            </div>
        </div>
    </div>
</script>