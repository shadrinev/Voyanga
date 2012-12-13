<?php $link = Yii::app()->createAbsoluteUrl('/user/orders'); ?>
<p>Дорогой друг!</p>
<p>Для Вас создан личный кабинет на сайте Voyanga: <?php echo CHtml::link($link, $link); ?> <br />
Там будет храниться информация обо всех Ваших заказах и их статусах. <br />
</p> 

<p>Параметры для входа в <?php echo CHtml::link('личный кабинет', $link); ?>: <br />
--------- <br />
адрес электронной почты: <?php echo $email ?> <br />
пароль: <?php echo $password ?> <br />
---------</p>

