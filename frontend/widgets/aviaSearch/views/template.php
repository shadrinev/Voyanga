<?php $this->render('jsTemplate', array('model'=>new RouteForm, 'form'=>$form)); ?>
<?php $i=0; ?>
<fieldset>
    <legend>Перелёты</legend>
<div id='routesArea'>
    <?php foreach ($routes as $route): ?>
        <?php $this->render('_template', array('model'=>$route, 'i'=>$i++, 'form'=>$form)); ?>
    <?php endforeach ?>
    <?php if (sizeof($routes)==0) $this->render('_template', array('model'=>new RouteForm, 'i'=>$i++, 'form'=>$form)); ?>
</div>
<br>
<?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'primary',
    'size'=>'mini',
    'icon'=>'icon-plus',
    'label'=>'Добавить перелёт',
    'htmlOptions'=>array(
        'class' => 'addRoute',
        'data-counter' => $i
    ))
);
?>
<br><br>
</fieldset>