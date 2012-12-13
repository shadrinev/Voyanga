<?php $link = Yii::app()->createAbsoluteUrl('/user/orders'); ?>
<p>Дорогой друг!</p>
<p>Для Вас создан личный кабинет на сайте Voyanga: <?php echo CHtml::link($link, $link); ?> 
Там будет храниться информация обо всех Ваших заказах и их статусах.
</p> 

<p>Параметры для входа в <?php echo CHtml::link('личный кабинет', $link); ?>:
---------
адрес электронной почты: <?php echo $email ?>
пароль: <?php echo $password ?></p>
---------
