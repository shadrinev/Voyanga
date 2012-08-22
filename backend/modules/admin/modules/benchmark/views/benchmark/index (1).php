<?php
/**
 * The administration view for the {@link ABenchmark} model
 * @var ABenchmark $model The ABenchmark model used for searching
 */
$this->breadcrumbs = array(
    'Benchmarks'
);

$this->menu = array(
    array('label' => 'List ABenchmark', 'url' => array('index')),
    array('label' => 'Create ABenchmark', 'url' => array('create')),
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("/admin/benchmark/benchmark/create"),
        ),
        array(
            "label" => "Запустить все",
            "url" => array("/admin/benchmark/benchmark/run"),
        ),
    ),
    "title" => "Benchmarks"
));
?>
<p class='info box'>Benchmarks help ensure that changes you make to your application or server don't adversely affect
    performance.</p>
<?php $this->widget('bootstrap.widgets.BootGridView', array(
    'id' => 'abenchmark-grid',
    'type'=>'striped bordered condensed',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            "value" => '$data->createLink($data->getUrl(),null,array("class" => ($data->isRegression ? "warning icon" : ($data->isProgression ? "tick icon" : ""))))',
            "name" => "url",
            "header" => "URL",
            "type" => "raw",
        ),
        "lastResult.requestsPerSecond:number",
        array(
            "value" => 'round($data->difference,2)."&#37;;"',
            "type" => "raw",
            "header" => "Performance Difference",

        ),
        array(
            'value' => '"<span class=\"sparkline\">".implode(",",$data->getSparklineData())."</span>"',
            'type' => 'raw',
            "header" => "Performance History",
        ),
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
));

Yii::createComponent(array(
    "class" => "packages.sparklines.ASparklineWidget"
))->registerScripts();
Yii::app()->clientScript->registerScript("benchmarkSparklines", "$('.sparkline').sparkline('html', {width: '150px', chartRangeMin: 0});");
$this->endWidget();
?>