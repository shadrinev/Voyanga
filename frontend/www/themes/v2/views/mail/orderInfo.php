<!--- <?php $link = Yii::app()->createAbsoluteUrl('/user/orders'); ?> --->
<?php $link = 'http://www.test.voyanga.com/user/orders' ?>
<p>Ваш заказ готов!
<p>Все ваши заказы доступны по адресу: <?php echo CHtml::link($link, $link); ?>. </p>
<p>Спасибо!</p>
