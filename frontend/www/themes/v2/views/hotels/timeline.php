<?php

$theme = Yii::app()->theme->baseUrl;
?>
<script type="text/html" id="hotels-timeline-template">
    <div class="innerCalendar allTourCal">
        <div id='voyanga-calendar-timeline' class="hotel">
            <div class="weekDaysVoyanga">
                <div class="weekDaysVoyangaInner">
                    <div class="weekDayVoyanga"><div class="weekDayBorder"><span>Пн</span></div></div>
                    <div class="weekDayVoyanga"><div class="weekDayBorder"><span>Вт</span></div></div>
                    <div class="weekDayVoyanga"><div class="weekDayBorder"><span>Ср</span></div></div>
                    <div class="weekDayVoyanga"><div class="weekDayBorder"><span>Чт</span></div></div>
                    <div class="weekDayVoyanga"><div class="weekDayBorder"><span>Пт</span></div></div>
                    <div class="weekDayVoyanga weekEnd "><div class="weekDayBorder"><span>Сб</span></div></div>
                    <div class="weekDayVoyanga weekEnd "><div class="weekDayBorder"><span>Вс</span></div></div>
                </div>
            </div>
            <div class="calendarUnscrollVoyanga">
                <div class="calendarGridVoyanga">
                    <div class="calendarDIVVoyanga">

                    </div>
                </div>
            </div>
            <div class="monthLineWrapper" style="position: relative;width: 100%">
                <div class="monthLineVoyangaYear">
                    <div class="monthLineVoyanga BackPlane">
                    </div>
                    <div class="monthLineVoyanga">
                        <div class="knobVoyanga" id="voyangaCalendarKnob" style="left: 0%;"><img src="<?php echo $theme ?>/images/bg-knob.png" width="100%"></div>
                    </div>
                    <div class="knobUpAllMonth"></div>
                </div>

            </div>
        </div>
    </div>
</script>