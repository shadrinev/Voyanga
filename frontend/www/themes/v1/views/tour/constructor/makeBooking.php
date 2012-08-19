<?php if($validFill): ?>
    <?php if($validBooking): ?>
        Все элементы успешно забронированы
    <?php else: ?>
        не все элементы забронированы
        <?php foreach($elements as $element): ?>
            не удалось забронировать <?php echo $element['type']?> с id <?php echo $element['id']?> статус <?php echo $element['status']?></br />
        <?php endforeach; ?>
    <?php endif; ?>
<?php else: ?>
    не все элементы корзины заполнены
<?php endif; ?>
