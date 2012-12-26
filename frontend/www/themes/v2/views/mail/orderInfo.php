<!--- <?php $link = Yii::app()->createAbsoluteUrl('/user/orders'); ?> --->
<?php $link = 'http://www.test.voyanga.com/user/orders' ?>
<p>Дорогой друг!</p>
<p>Ваш заказ готов. Все заказы доступны по адресу: <?php echo CHtml::link($link, $link); ?>. </p>
<p>К письму прикреплены выписанные электронные билеты.</p>
<p>Спасибо!</p>
