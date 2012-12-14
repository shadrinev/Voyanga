<?php $link = Yii::app()->createAbsoluteUrl('/user/orders'); ?>
<p>Ваш заказ оформленный на начем сайте готов!
<p>Все ваши заказы на нашем сайте доступны по адресу: <?php echo CHtml::link($link, $link); ?>. </p>
<p>Спасибо!</p>