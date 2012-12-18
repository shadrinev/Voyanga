<script type="text/javascript">
    $(function(){
        window.app.runWithModule('tours');
    });
</script>
<div class="center-block">
    <div class="main-block black" id="content">
        <h1>Мои заказы</h1>
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'drives-grid',
            'dataProvider' => $model,
            'template' => "{items}",
            'columns' => array(
                array(
                    'header' => 'Дата заказа',
                    'value' => 'date("d/m/Y H:i", strtotime($data->timestamp))',
                ),
                array(
                    'header' => 'Номер заказа',
                    'value' => 'CHtml::link("Заказ &#8470;".$data->readableId, array("/buy/order/id/".$data->secretKey))',
                    'type' => 'raw'
                )
            ),
        )); ?>
    </div>
</div>