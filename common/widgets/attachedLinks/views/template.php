<?php $this->render('jsTemplate', array('model'=>new EventLink, 'form'=>$form)); ?>
<?php $i=0; ?>
<fieldset>
    <legend>Ссылки</legend>
<div id='linksArea'>
    <?php foreach ($links as $link): ?>
        <?php $this->render('_template', array('model'=>$link, 'i'=>$i++, 'form'=>$form)); ?>
    <?php endforeach ?>
    <?php if (sizeof($links)==0) $this->render('_template', array('model'=>new EventLink, 'i'=>$i++, 'form'=>$form)); ?>
</div>
<br>
<?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'primary',
    'size'=>'mini',
    'icon'=>'icon-plus',
    'label'=>'Добавить ссылку',
    'htmlOptions'=>array(
        'class' => 'addLink',
        'data-counter' => $i
    ))
);
?>
<br><br>
</fieldset>