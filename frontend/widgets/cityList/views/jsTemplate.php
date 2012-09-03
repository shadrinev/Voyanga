<script id='itemTemplate' type='voyanga/template'>
    <?php $this->render('_template', array(
        'newItem'=>true,
        'model' => new EventStartCityForm(),
        'i'=>"{{i}}",
        'item'=>new EventStartCityForm(),
        'attribute'=>$attribute,
        'attributeId'=>$attributeId,
        'attributeReadable'=>$attributeReadable,
        'form'=>$form
    )); ?>
</script>