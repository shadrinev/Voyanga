<?php $this->render('jsTemplate', array('model'=>new TripForm, 'form'=>$form)); ?>
<?php $i=0; ?>
<fieldset>
    <legend>Перелёты</legend>
<div id='tripsArea'>
    <?php foreach ($trips as $trip): ?>
        <?php $this->render('_template', array('model'=>$trip, 'i'=>$i++, 'form'=>$form)); ?>
    <?php endforeach ?>
    <?php if (sizeof($trips)==0) $this->render('_template', array('model'=>new TripForm, 'i'=>$i++, 'form'=>$form)); ?>
</div>
<br>
<?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'primary',
    'size'=>'mini',
    'icon'=>'icon-plus',
    'label'=>'Добавить перелёт',
    'htmlOptions'=>array(
        'class' => 'addtrip',
        'data-counter' => $i
    ))
);
?>
<br><br>
</fieldset>