<div class="userbar" style="float: right">
    <?php if (Yii::app()->user->isGuest): ?>
        <span class="loginLink">
            <?php echo CHtml::link('Войти', array('/user/login')); ?>
        </span>
    <?php else: ?>
        <span class="username">
            <?php echo Yii::app()->user->model->email; ?>
        </span>
        <ul class="usermenu">
            <li><?php echo CHtml::link('Мои заказы', array('/user/orders')); ?></li>
            <li><?php echo CHtml::link('Выйти', array('/user/logout')); ?></li>
        </ul>
    <?php endif ?>
</div>