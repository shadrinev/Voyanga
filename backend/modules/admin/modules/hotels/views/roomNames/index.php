<?php
$this->breadcrumbs=array(
	'Hotels',
);

$this->menu=array(
	array('label'=>'Create Event','url'=>array('create')),
	array('label'=>'Manage Event','url'=>array('admin')),
);
?>

<h1>Events</h1>
<form method="get">
    <input type="text" name="filterName" value="<?php echo $filterName; ?>">
    <select name="rusId" value="<?php echo $rusId; ?>">
        <option value="1">не важно</option>
        <option value="2">null</option>
        <option value="3">not null</option>
    </select>
    <input type="submit" name="smb" value="Ok">
</form>

<form method="post">
<?php $this->widget('bootstrap.widgets.BootGridView',array(
    'id'=>'event-grid',
    'dataProvider'=>$dataProvider,
    //'filter'=>$model,
    'columns'=>array(
        array(
            'header'=>'id',
            'value'=>'$data->id'
        ),
        array(
            'header'=>'roomName',
            'value'=>'$data->roomNameCanonical'
        ),
        array(
            'header'=>'Размер',
            'value'=>'$data->roomSizeId'
        ),
        array(
            'header'=>'Тип',
            'value'=>'$data->roomTypeId'
        ),
        array(
            'header'=>'RusId',
            'value'=>'$data->roomNameRusId'
        ),
        array(
            'header'=>'RusName',
            'value'=>'$data->rusName'
        ),

        array(
            'class'=>'zii.widgets.grid.CCheckBoxColumn',
            'checked'=>'false',
            'selectableRows'=>2,
            'id'=>'roomNameIds',


        ),
    ),
)); ?>
    <input type="text" name="rusNameId" value="">
    <input type="submit" name="smbset" value="Ok">
    <input type="submit" name="smbunset" value="убрать привязку">
</form>
