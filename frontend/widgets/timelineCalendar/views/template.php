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