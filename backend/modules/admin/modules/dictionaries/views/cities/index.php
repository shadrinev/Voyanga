<?php
$this->breadcrumbs=array(
	'Hotels',
);

$this->menu=array(
	array('label'=>'Create Event','url'=>array('create')),
	array('label'=>'Manage Event','url'=>array('admin')),
);
?>

<h1>Сопоставление русских названий и названий hotelbook</h1>
<form method="get">
    <input type="text" name="filterName" value="<?php echo $filterName; ?>">
    <select name="rusId" value="<?php echo $rusId; ?>">
        <option value="1">не важно</option>
        <option value="2">null</option>
        <option value="3">not null</option>
    </select>
    <input type="submit" name="smb" value="Ok">
</form>
<script>
    function convertRoomNameToCanoical(){
        var roomName = $('#originalRoomName').val()
        if(roomName){
            $.ajax({
                url: "/admin/hotels/roomNames/getCanonicalName/roomName/"+ roomName,
                dataType: 'json',
                type: 'post',
                beforeSend: function(){
                    $('#roomNameCanonicalResult').val('Идет запрос...');
                },
                success: function(data, textStatus, jqXHR){
                    $('#roomNameCanonicalResult').val(data.roomNameCanonical);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    $('#roomNameCanonicalResult').val('Ошибка!');
                }
            });
        }
    }
</script>
<input type="text" id="originalRoomName" placeholder="Room name">
<input type="text" id="roomNameCanonicalResult" placeholder="Room Name Canonical">
<input type="submit" onclick="convertRoomNameToCanoical()" value="Преобразовать">

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
            'value'=>'$data->roomSize'
        ),
        array(
            'header'=>'Тип',
            'value'=>'$data->roomType'
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
    <input type="text" name="rusNameId" id="rusNameId" value="" placeholder="id" style="width: 30px;">
    <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
    'options'=>array(
        'items'=>10,
        'ajax' => array(
            'url' => "/admin/hotels/roomNames/rusRoomNames",
            'timeout' => 5,
            'displayField' => "value",
            'triggerLength' => 2,
            'method' => "get",
            'loadingClass' => "loading-circle",
        ),
        'onselect'=>'js:function(res){console.log(res);$("#rusNameId").val(res.id);document.idChange();}',
        'matcher'=>'js:function(){return true}',
    ),
    'htmlOptions'=>array(
        'value'=>'',
        'id'=>'roomNameRusField'
    )
)); ?>
    <input type="submit" name="smbset" value="Ok">
    <input type="submit" name="smbunset" value="убрать привязку">
</form>
