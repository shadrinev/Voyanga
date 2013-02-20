<!--- <?php $link = Yii::app()->createAbsoluteUrl('/user/orders'); ?> --->
<?php $link = 'http://www.voyanga.com/user/orders' ?>
<p>Ваш заказ отменен. Все заказы доступны по адресу: <?php echo CHtml::link($link, $link); ?>. </p>
<p>Оставайтесь с нами!</p>
