<?php $this->beginWidget('common.extensions.handlebars.HandlebarsWidget', array('id'=>'flight-search', 'compileVariable' => 'result')) ?>
<h3>Результаты поиска {{name}} {{surname}}</h3>
<?php $this->endWidget(); ?>

<!-- example of usage
<script type="text/javascript">
    var context = <?php echo json_encode(array('name'=>'Mikhail', 'surname'=> 'Kuklin')) ?>;
</script>
<?php //$this->widget('common.extensions.handlebars.HandlebarsOutput', array('id'=>'flight-search-result', 'compileVariable' => 'result', 'contextVariable' => 'context')) ?>
-->