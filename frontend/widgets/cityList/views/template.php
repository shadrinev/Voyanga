<?php $this->render('jsTemplate', array(
    'newItem'=>true,
    'model'=>$newItem,
    'form'=>$form,
    'attribute'=>$attribute,
    'attributeId'=>$attributeId,
    'attributeReadable'=>$attributeReadable
)); ?>
<?php $i=0; ?>
<div id='itemsArea'>
    <?php foreach ($model->$attribute as $item): ?>
        <?php $this->render('_template', array(
            'newItem'=>false,
            'model'=>$item,
            'i'=>$i++,
            'form'=>$form,
            'attributeId'=>$attributeId,
            'attributeReadable'=>$attributeReadable
    )); ?>
    <?php endforeach ?>
    <?php if (sizeof($model->$attribute)==0)
        $this->render('_template', array(
            'newItem'=>true,
            'model'=>$newItem,
            'i'=>$i++,
            'form'=>$form,
            'attributeId'=>$attributeId,
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