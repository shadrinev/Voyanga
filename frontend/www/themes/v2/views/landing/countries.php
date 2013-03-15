<script>
    initLandingPage = function() {
        var app, avia, hotels, tour;
        window.voyanga_debug = function() {
            var args;
            args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
            return console.log.apply(console, args);
        };
        app = new Application();
        avia = new AviaModule();
        hotels = new HotelsModule();
        tour = new ToursModule();
        window.app = app;
        app.register('tours', tour, true);
        app.register('hotels', hotels);
        app.register('avia', avia);
        app.runWithModule('tours');
        app.activeModule('tours');
        ko.applyBindings(app);
        ko.processAllDeferredBindingUpdates();
    };

    $(document).ready(function(){
        initLandingPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })
</script>
<div class="headBlockOne">
    <?php
    $this->widget('common.components.BreadcrumbsVoyanga', array(
        'links'=>$this->breadLinks,
        'separator'=>' &rarr; ',
        'homeLink'=>CHtml::link('Главная','/'),
        'htmlOptions' => array(
            'class' => 'breadcrumbs'
        )
    ));
    ?>
<div class="center-block">
    <h1>Авиабилеты и отели по всему миру!</h1>

</div>
<?php
    $firstHalf = ceil(count($countries)/2);
    $secondHalf = count($countries) - $firstHalf;?>
<table class="tableFlight first up">
    <tbody>
    <?php
    $i =0;
    foreach($countries as $country):
        $i++;
        if($i <= $firstHalf):
        ?>
        <tr class="<?php echo (($i+1) % 2 == 0)? 'select' : '' ;?>">
            <td class="tdEmpty">

            </td>
            <td class="tdFlight">
                <div><?php echo $country->localRu;?></div>
            </td>
            <td class="tdHostel">
                <a href="/land/hotels/<?php echo $country->code;?>">отели</a>
            </td>
            <td class="tdFly">
                <a href="/land/<?php echo $country->code;?>">перелеты</a>
            </td>
        </tr>
        <?php
        else:
            break;
        endif;
    endforeach;?>
    </tbody>
</table>
<table class="tableFlight second up">
    <tbody>
    <?php
    $i =0;
    foreach($countries as $country):
        $i++;
        if($i > $firstHalf):
            ?>
        <tr class="<?php echo (($i+1) % 2 == 0)? 'select' : '' ;?>">

            <td class="tdFlight">
                <div><?php echo $country->localRu;?></div>
            </td>
            <td class="tdHostel">
                <a href="/land/hotels/<?php echo $country->code;?>">отели</a>
            </td>
            <td class="tdFly">
                <a href="/land/<?php echo $country->code;?>">перелеты</a>
            </td>
            <td class="tdEmpty">

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
