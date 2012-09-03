<?php $this->render('jsTemplate', array(
    'model'=>$newItem,
    'form'=>$form,
    'attribute'=>$attribute,
    'attributeReadable'=>$attributeReadable
)); ?>
<?php $i=0; ?>
<div id='itemsArea'>
    <?php foreach ($items as $item): ?>
        <?php $this->render('_template', array(
            'newItem'=>false,
            'model'=>$model,
            'item'=>$item,
            'i'=>$i++,
            'form'=>$form,
            'attribute'=>$attribute,
            'attributeReadable'=>$attributeReadable
    )); ?>
    <?php endforeach ?>
    <?php if (sizeof($items)==0)
        $this->render('_template', array(
            'model'=>$newItem,
            'i'=>$i++, 'form'=>$form,
            'attribute'=>$attribute,
            'attributeReadable'=>$attributeReadable
        )); ?>
</div>
<br>
<?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'primary',
    'size'=>'mini',
    'icon'=>'icon-plus',
    'label'=>'Добавить',
    'htmlOptions'=>array(
        'class' => 'addItem',
        'data-counter' => $i
    ))
);
?>