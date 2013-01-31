<div class="headBlockOne">
<div class="center-block">
    <h1>Авиабилеты в <?php echo $countryMorph['caseAcc'];?></h1>
    <h3>Стоимость из
        <?php
        foreach($citiesFrom as $cityPoint):
            ?>
            <a href="#" class="cityChoise<?php echo $cityPoint['cityId']==$currentCity->id ? ' active':'';?>">
                <span><?php echo $cityPoint['cityName'];?></span>
            </a>
            <?php

        endforeach;
        ?>
    </h3>
</div>
<table class="tableFlight first up">
    <thead>
    <tr>
        <td class="tdEmpty">

        </td>
        <td class="tdFlight">
            Рейс
        </td>
        <td class="tdTo">
            Туда
        </td>
        <td class="tdFrom">
            Обратно
        </td>
        <td class="tdPrice">
            Цена
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
    $firstHalf = round(count($flightCache)/2);
    $secondHalf = count($flightCache) - $firstHalf;
    $i =0;
    foreach($flightCache as $fc):
        $i++;
        if($i <= $firstHalf):
            $back = ($fc->dateBack == '0000-00-00' ? false : true );
            ?>
    <tr<?php echo (($i+1) % 2) == 0 ? ' class="select"' : '';?>>
        <td class="tdEmpty">

        </td>
        <td class="tdFlight">
            <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?></div>
        </td>
        <td class="tdTo">
                <?php echo date('d.m',strtotime($fc->dateFrom));?>
        </td>
        <td class="tdFrom">
            <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m',strtotime($fc->dateBack)) );?>
        </td>
        <td class="tdPrice">
            <a href="<?php echo '/land/'.City::getCityByPk($fc->to)->country->code.'/'.City::getCityByPk($fc->from)->code.'/'.City::getCityByPk($fc->to)->code.($fc->dateBack == '0000-00-00' ? '/trip/OW' : '' );?>"><span class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span class="rur">o</span></a>
        </td>
    </tr>
            <?php
        endif;
    endforeach;?>
    </tbody>
</table>
<table class="tableFlight second up">
    <thead>
    <tr>

        <td class="tdFlight">
            Рейс
        </td>
        <td class="tdTo">
            Туда
        </td>
        <td class="tdFrom">
            Обратно
        </td>
        <td class="tdPrice">
            Цена
        </td>
        <td class="tdEmpty">

        </td>
    </tr>
    </thead>
    <tbody>
    <?php $i =0;
    foreach($flightCache as $fc):
    $i++;
    if($i > $firstHalf):
        $back = ($fc->dateBack == '0000-00-00' ? false : true );?>
    <tr<?php echo ($i % 2) == 0 ? ' class="select"' : '';?>>
        <td class="tdFlight">
            <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?></div>
        </td>
        <td class="tdTo">
            <?php echo date('d.m',strtotime($fc->dateFrom));?>
        </td>
        <td class="tdFrom">
            <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m',strtotime($fc->dateBack)) );?>
        </td>
        <td class="tdPrice">
            <a href="<?php echo '/land/'.City::getCityByPk($fc->to)->country->code.'/'.City::getCityByPk($fc->from)->code.'/'.City::getCityByPk($fc->to)->code.($fc->dateBack == '0000-00-00' ? '/trip/OW' : '' );?>"><span class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span class="rur">o</span></a>
        </td>
    </tr>
    <?php
    endif;
    endforeach;?>

    </tbody>
</table>
<div class="clear"></div>

</div>
<div class="headBlockTwo">
<div class="center-block">
<h2>Отели в Москве</h2>

<?php foreach($hotelsInfo as $hotInfo):?>
<div class="hotels-tickets parkPage">
    <div class="content">
        <div class="full-info">
            <div class="preview-photo">
                <ul>
                    <li><a class="photo" href="<?php echo '/land/hotel/'.$hotInfo->hotelId;?>"><img src="<?php echo $hotInfo->getFrontImageUrl();?>"></a></li>
                </ul>
            </div>
            <div class="stars <?php echo $hotInfo->getWordStars();?>"></div>
            <div class="overflowBlock">
                <h4><?php echo $hotInfo->hotelName;?></h4>
                <div class="street">
                    <span><?php echo $hotInfo->address;?></span>
                    <span class="gradient"></span>
                </div>
            </div>
            <div class="how-cost">
                от <span class="cost"><?php echo $hotInfo->price;?></span> <span class="rur">o</span> / ночь
            </div>
        </div>
    </div>
    <span class="lt"></span>
    <span class="rt"></span>
    <span class="lv"></span>
    <span class="rv"></span>
    <span class="bh"></span>
</div>
<?php endforeach; ?>


<div class="clear"></div>
</div>
</div>
<div class="headBlockOne">
<div class="center-block">
    <h2 class="tableH2">Дешевые билеты из <?php echo $currentCity->caseGen;?></h2>
</div>
<table class="tableFlight first">
    <thead>
    <tr>
        <td class="tdEmpty">

        </td>
        <td class="tdFlight">
            Рейс
        </td>
        <td class="tdTo">
            Туда
        </td>
        <td class="tdFrom">
            Обратно
        </td>
        <td class="tdPrice">
            Цена
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
    $firstHalf = round(count($flightCacheFromCurrent)/2);
    $secondHalf = count($flightCacheFromCurrent) - $firstHalf;
    $i =0;
    foreach($flightCacheFromCurrent as $fc):
        $i++;
        if($i <= $firstHalf):
            $back = ($fc->dateBack == '0000-00-00' ? false : true );
            ?>
        <tr<?php echo (($i+1) % 2) == 0 ? ' class="select"' : '';?>>
            <td class="tdEmpty">

            </td>
            <td class="tdFlight">
                <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?></div>
            </td>
            <td class="tdTo">
                <?php echo date('d.m',strtotime($fc->dateFrom));?>
            </td>
            <td class="tdFrom">
                <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m',strtotime($fc->dateBack)) );?>
            </td>
            <td class="tdPrice">
                <a href="<?php echo '/land/'.City::getCityByPk($fc->to)->country->code.'/'.City::getCityByPk($fc->from)->code.'/'.City::getCityByPk($fc->to)->code.($fc->dateBack == '0000-00-00' ? '/trip/OW' : '' );?>"><span class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span class="rur">o</span></a>
            </td>
        </tr>
            <?php
        endif;
    endforeach;?>
    </tbody>
</table>
<table class="tableFlight second">
    <thead>
    <tr>

        <td class="tdFlight">
            Рейс
        </td>
        <td class="tdTo">
            Туда
        </td>
        <td class="tdFrom">
            Обратно
        </td>
        <td class="tdPrice">
            Цена
        </td>
        <td class="tdEmpty">

        </td>
    </tr>
    </thead>
    <tbody>
    <?php $i =0;
    foreach($flightCacheFromCurrent as $fc):
        $i++;
        if($i > $firstHalf):
            $back = ($fc->dateBack == '0000-00-00' ? false : true );?>
        <tr<?php echo ($i % 2) == 0 ? ' class="select"' : '';?>>
            <td class="tdFlight">
                <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?></div>
            </td>
            <td class="tdTo">
                <?php echo date('d.m',strtotime($fc->dateFrom));?>
            </td>
            <td class="tdFrom">
                <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m',strtotime($fc->dateBack)) );?>
            </td>
            <td class="tdPrice">
                <a href="<?php echo '/land/'.City::getCityByPk($fc->to)->country->code.'/'.City::getCityByPk($fc->from)->code.'/'.City::getCityByPk($fc->to)->code.($fc->dateBack == '0000-00-00' ? '/trip/OW' : '' );?>"><span class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span class="rur">o</span></a>
            </td>
        </tr>
            <?php
        endif;
    endforeach;?>

    </tbody>
</table>
<div class="clear"></div>
</div>
<div class="headBlockTwo" style="margin-bottom: 60px">
    <div class="center-block textSeo">
        <h2>Что такое Voyanga</h2>
        <p>Voyanga.com — это самый простой, удобный и современный способ поиска и покупки авиабилетов. Мы постоянно работаем над развитием и улучшением сервиса. Наш сайт подключен сразу к нескольким системам бронирования, что позволяет сравнивать тарифы и подбирать наиболее выгодные и удобные тарифы и рейсы.</p>
        <p>Наша компания официально аккредитована в Международной ассоциации авиаперевозчиков (IATA) и в российской транспортной клиринговой палате (ТКП). Мы прошли все необходимые процедуры для оформления электронных билетов на рейсы российских и зарубежных авиакомпаний.</p>
        <p>Помимо сайта у нас есть собственная служба бронирования, которая находится в нашем офисе. Всегда можно позвонить и вам помогут и ответят на все вопросы. Офис компании находится в Санкт-Петербурге.</p>
        <h2>Как посетить 10 стран по цене Айфона</h2>
        <p>Voyanga.com — это самый простой, удобный и современный способ поиска и покупки авиабилетов. Мы постоянно работаем над развитием и улучшением сервиса. Наш сайт подключен сразу к нескольким системам бронирования, что позволяет сравнивать тарифы и подбирать наиболее выгодные и удобные тарифы и рейсы.</p>
        <p>Наша компания официально аккредитована в Международной ассоциации авиаперевозчиков (IATA) и в российской транспортной клиринговой палате (ТКП). Мы прошли все необходимые процедуры для оформления электронных билетов на рейсы российских и зарубежных авиакомпаний.</p>
        <p>Помимо сайта у нас есть собственная служба бронирования, которая находится в нашем офисе. Всегда можно позвонить и вам помогут и ответят на все вопросы. Офис компании находится в Санкт-Петербурге.</p>

    </div>
</div>
<div class="clear"></div>