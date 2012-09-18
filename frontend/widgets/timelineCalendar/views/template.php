<div id='timelineCalendar'>
    <div class="weekDays">
        <div class="weekDay">Пн</div>
        <div class="weekDay">Вт</div>
        <div class="weekDay">Ср</div>
        <div class="weekDay">Чт</div>
        <div class="weekDay">Пт</div>
        <div class="weekDay">Сб</div>
        <div class="weekDay">Вс</div>
    </div>
    <div class="calendarUnscroll">
        <div class="calendarGrid">

        </div>
    </div>
    <div class="monthLine"><div class="knob" id="timelineCalendarKnob" style="left: 0%;"></div></div>

</div>

<!-- CALENDAR -->
<div class="innerCalendar allTourCal">
    <div id='voyanga-calendar-timeline' class="hotel">
        <div class="weekDaysVoyanga">
            <div class="weekDaysVoyangaInner">
                <div class="weekDayVoyanga"><span>Понедельник</span></div>
                <div class="weekDayVoyanga"><span>Вторник</span></div>
                <div class="weekDayVoyanga"><span>Среда</span></div>
                <div class="weekDayVoyanga"><span>Четверг</span></div>
                <div class="weekDayVoyanga"><span>Пятница</span></div>
                <div class="weekDayVoyanga weekEnd "><span>Суббота</span></div>
                <div class="weekDayVoyanga weekEnd "><span>Воскресенье</span></div>
            </div>
        </div>
        <div class="calendarUnscrollVoyanga">
            <div class="calendarGridVoyanga">
                <div class="calendarDIVVoyanga">

                </div>
            </div>
        </div>
        <div class="monthLineVoyanga"><div class="knobUpAllMonth"></div><div class="knobVoyanga" id="voyangaCalendarKnob" style="left: 0%;"></div></div></div>
</div>
</div>
<!-- END CALENDARE -->
<?php $cs = Yii::app()->clientScript; $cs->registerScript('timelineinit', "
    /**
     * Event have
     * Event.dayStart
     * Event.dayEnd
     * Event.type (flight/hotel)
     * Event.color
     * Event.description
     *
     * @type {Array}
     */

    TimelineCalendar.calendarEvents = ".$timelineEvents."
    TimelineCalendar.init();

", CClientScript::POS_READY);