<h1>Оплата бронированиея номер №<?php echo $booking->id;?></h1>
<p>
Данные перелета:
<ul>
    <li>Тип: <?php echo $voyage->isComplex() ? 'Сложный': (count($voyage->flights) == 2 ? 'Туда - Обратно' : 'Прямой');?></li>
    <li>Направление:<div style="white-space:nowrap;">
        <?php foreach($voyage->flights as $flight):?>
            <div style='color: #ff0000;white-space:nowrap;'><?php echo UtilsHelper::dateToPointDate($flight->departureDate);?></div>
            <div style='color: #0000ff;white-space:nowrap;'><?php echo $flight->departureCity->localRu;?> -
            <?php echo $flight->arrivalCity->localRu;?>;</div>
        <?php endforeach; ?>
        </div>
    </li>
    <li>Время в пути: <?php echo $voyage->getFullDuration();?></li>
</ul>
</p>
<a href='/site/buyOrder/order/<?php echo $booking->id;?>'>Оплатить</a>
