<script type="text/javascript">
    window.apiEndPoint = '<?php echo Yii::app()->params['api.endPoint'] ?>';
</script>
<div class="head" id="header">
    <!-- CENTER BLOCK -->
    <div class="center-block">
        <a href="/" class="logo">Voyanga</a>
        <a href="javascript:void(0)" onclick="openPopUpProj()" class="about">О проекте</a>

        <div class="telefon">
            <span class="prefix">+7 (499)</span> 533-09-33
        </div>
        <div class="slide-turn-mode">
            <div class="switch"><span class="l"></span><span class="c"></span><span class="r"></span></div>
            <div class="bg-mask"></div>

            <ul>
                <li id="h-tours-slider" class="planner btn" data-bind="click: slider.click"><a href="/#tours">Планировщик</a>
                </li>
                <li id="h-avia-slider" class="aviatickets btn" data-bind="click: slider.click"><a href="/#avia">Авиабилеты</a>
                </li>
                <li id="h-hotels-slider" class="hotel btn" data-bind="click: slider.click"><a href="/#hotels">Отели</a>
                </li>
            </ul>
        </div>

        <?php if (Yii::app()->user->isGuest): ?>
        <div class="login-window full" onclick="openPopUpLogIn('enter')">
            <div class="registerForm">
                <a href="javascript:void(0)" class="logInLinks">
                    <span class="text">Регистрация и вход</span>
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="login-window full" onclick="showUserMenu()">
            <div class="registerForm">
                <a href="javascript:void(0)" class="logInLinks user">
                    <span class="text"><?php echo Yii::app()->user->model->email; ?></span>
                </a>
            </div>
            <div class="popupDown">
                <a href="/user/orders">Мои заказы</a>
                <a href="/user/logout">Выйти</a>
            </div>
        </div>
        <?php endif ?>
    </div>
    <!-- END CENTER BLOCK -->
</div>