

<h1>Calendar</h1>

<p>
If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.
</p>
    <?php

$dt = new DateTime('1983-10-03');
$interval = new DateInterval('P200D');
echo $dt->format('Y-m-d H:i');
$dt->add($interval);
echo $dt->format('Y-m-d H:i');

$input = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank");
$rand_keys = array_rand($input, 2);
echo $input[$rand_keys[0]] . "\n";
echo $input[$rand_keys[1]] . "\n";

?>
kjhjkhkl
<div class="form">

<?php
$this->widget('site.frontend.widgets.timelineCalendar.TimelineCalendarWidget', array(
    'eventsTimeline'=>'contact-form',
));
    ?>

</div><!-- form -->

