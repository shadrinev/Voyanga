<?php $this->render('jsTemplate', array('model'=>new HotelRoomForm, 'form'=>$form)); ?>
<?php $i=0; ?>
<fieldset>
    <legend>Комнаты</legend>
<div class='linksArea'>
    <?php foreach ($rooms as $room): ?>
        <?php $this->render('_template', array('model'=>$room, 'i'=>$i++, 'form'=>$form)); ?>
    <?php endforeach ?>
    <?php if (sizeof($rooms)==0) $this->render('_template', array('model'=>new HotelRoomForm, 'i'=>$i++, 'form'=>$form)); ?>
</div>
<br>
<?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'primary',
    'size'=>'mini',
    'icon'=>'icon-plus',
    'label'=>'Добавить комнату',
    'htmlOptions'=>array(
        'class' => 'addRoom',
        'data-counter' => $i
    ))
);
?>
<br><br>
</fieldset>