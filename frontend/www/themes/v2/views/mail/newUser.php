<?php $link = Yii::app()->createAbsoluteUrl('/user/orders'); ?>
<p>Вы зарегистрированы на сайте <?php echo CHtml::link('Voyanga.com', Yii::app()->createAbsoluteUrl('/')); ?></p>
<p>Ваш логин: <?php echo $email ?></p>
<p>Ваш пароль: <?php echo $password ?></p>
<p>Все ваши заказы на нашем сайте доступны по адресу: <?php echo CHtml::link($link, $link); ?>. </p>
<p>Спасибо!</p>