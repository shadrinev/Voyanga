<table class="table table-bordered" id='detail-' style='background-color: #f0f0f0'>
    <thead>
    <th>Вылет</th>
    <th>Прилёт</th>
    <th>Авиакомпания</th>
    <th>Продолжительность полёта</th>
    </thead>
    <tbody>
    <?php foreach($flight['flights'] as $flightElement):?>
        <?php foreach($flightElement['flightParts'] as $flightPart):?>
        <tr>
            <td><?php echo $flightPart['datetimeBegin'];?>, <?php echo $flightPart['departureCity'];?>, <?php echo $flightPart['departureAirport'];?></td>
            <td><?php echo $flightPart['datetimeEnd'];?>, <?php echo $flightPart['arrivalCity'];?>, <?php echo $flightPart['arrivalAirport'];?></td>
            <td><img src='http://test.voyanga.com/img/airlines/<?php echo $flightPart['transportAirline'];?>.png'></td>
            <td><?php echo floor($flightPart['duration'] / 3600).' ч '.floor(($flightPart['duration'] % 3600) /60).' мин';?></td>
        </tr>
        <?php endforeach;?>
    <?php endforeach;?>
    </tbody>
</table>
