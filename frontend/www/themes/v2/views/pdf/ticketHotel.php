<?php
$dayOfWeek = array('воскресенье','понедельник','вторник','среда','четверг','пятница','суббота');
$monthNames = array('','января','февраля','марта','апреля','мая','июня','июля','августа','сентыбря','октября','ноября','декабря');
$dayFormat = "d|n|w";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>New Document</title>
    <link href="css/reset.style.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="pages">
    <div class="content">
        <table class="header">
            <tr>
                <td class="tdLogo"><img src="images/logo.png" width="209" height="50"></td>
                <td class="tdEticket">Подтверждение<br>бронирования</td>
                <td class="tdTelefon">+7 (499) 553-09-33</td>
                <td class="tdLink"><a href="http://www.voyanga.com">www.voyanga.com</a><br>
                    <a href="mailto:support@voyanga.com">support@voyanga.com</a></td>
            </tr>
        </table>
        <table class="hederInfo">
            <thead>
            <tr>
                <td class="tdNumberVoyangaHotel">
                    Номер заказа<br>
                    Order Number
                </td>
                <td class="tdReserveHotel">
                    Номер бронирования<br>
                    Booking number
                </td>
                <td class="tdInfoPassengerHotel">
                    Сведения о пассажирах<br>
                    Traveler Information
                </td>
            </tr>
            </thead>
            <tbody>
            <?php foreach($hotelPassports as $pKey=>$passport):?>
                <tr>
                    <td class="tdNumberVoyangaHotel">
                        <?php if($pKey===0): ?>
                            <strong><?php echo $bookingId; ?></strong>
                        <?php endif; ?>
                    </td>
                    <td class="tdReserveHotel">
                        <div class="divReserveHotel"><strong><?php echo $pnr; ?></strong></div>
                    </td>
                    <td class="tdInfoPassengerHotel">
                        <div class="passenger"><?php echo strtoupper($passport->lastName.' '.$passport->firstName.' '.($passport->genderId == Passport::GENDER_M ? 'MR' : 'MS')); ?></div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <img src="images/vertLine.png" class="hr">
        <h2 style="margin-bottom: 20px"><?php echo $ticket->hotelName;?> <?php echo $ticket->categoryId.'*';?></h2>
        <table>
            <tr>
                <td class="tdMaps">
                    <div class="maps">
                        <img src="http://maps.googleapis.com/maps/api/staticmap?zoom=15&size=310x259&maptype=roadmap&markers=icon:http://test.voyanga.com/themes/v2/images/pin1.png%7Ccolor:red%7Ccolor:red%7C%7C<?php echo $ticket->latitude.','.$ticket->longitude;?>&sensor=false">
                    </div>
                </td>
                <td class="tdInfo">
                    <table class="hotelInfo">
                        <tr>
                            <td width="85" style="font-size: 13px">Адрес
                                <br>Adress</td>
                            <td class="street"><?php echo $ticket->address;?></td>
                        </tr>
                    </table>
                    <?php
                    $timestamp = strtotime($ticket->checkIn);
                    $dayPrint = date($dayFormat,$timestamp);
                    $dateString ='';
                    list($dd,$mm,$ww) = explode('|',$dayPrint);
                    $dateString ="<span class='f20'>$dd {$monthNames[$mm]}</span>, {$dayOfWeek[$ww]}";

                    ?>
                    <table class="hotelInfo">
                        <tr>
                            <td width="85" style="font-size: 13px">Заселение<br>Incoming</td>
                            <td class="dates"><?php echo $dateString;?><?php echo $hotelInfo->earliestCheckInTime ? ', с '.$hotelInfo->earliestCheckInTime : '';?></td>
                        </tr>
                    </table>
                    <?php
                    $timestamp += $ticket->duration*3600*24;
                    $dayPrint = date($dayFormat,$timestamp);
                    $dateString ='';
                    list($dd,$mm,$ww) = explode('|',$dayPrint);
                    $dateString ="<span class='f20'>$dd {$monthNames[$mm]}</span>, {$dayOfWeek[$ww]}";

                    ?>
                    <table class="hotelInfo">
                        <tr>
                            <td width="85" style="font-size: 13px">Выезд<br>Leaving</td>
                            <td class="dates"><?php echo $dateString;?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <h2 style="margin-bottom: 10px"><?php echo (count($ticket->rooms) > 1 ? "Ваши номера" : "Ваш номер");?></h2>
        <img src="images/vertLine.png" class="hr">
        <?php foreach($ticket->rooms as $roomKey=>$room): ?>
            <h3 style="margin-top: 10px">
                <?php
                if(!$room->roomName){
                    echo $room->showName;
                }else{
                    echo $room->roomName.($room->showName !== $room->roomName ? " / {$room->showName}": '');
                }
                ?>
            </h3>
            <table class="hotelGuests">
                <tr>
                    <td width="70" style="font-size: 13px">Гости<br>Guests</td>
                    <td>
                        <?php foreach($hotelPassports as $pKey=>$passport):?>
                            <?php if($passport->roomKey == $roomKey): ?>
                                <div class="guest"><?php echo strtoupper($passport->lastName.' '.$passport->firstName); ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>
            <?php if($room->mealName):?>
                <div class="service">Питание: <?php echo $room->mealName;?></div>
            <?php endif;?>
            <?php if($room->mealBreakfastName):?>
                <div class="service">Типа завтрака: <?php echo $room->mealBreakfastName;?></div>
            <?php endif;?>

            <img src="images/vertLine.png" class="hr">
        <?php endforeach; ?>
        <?php if($ticket->cancelCharges): ?>
        <div class="bron">
            <?php if($ticket->cancelExpiration && $ticket->cancelExpiration > time()):?>
                <?php
                $timestamp = $ticket->cancelExpiration;
                $dayPrint = date('d|n|Y',$timestamp);
                $dateString ='';
                list($dd,$mm,$yy) = explode('|',$dayPrint);
                $dateString ="$dd {$monthNames[$mm]}  {$dayOfWeek[$ww]}";

                ?>
                Бесплатная отмена брони до <?php echo $dateString;?>.<br>
            <?php endif; ?>

            <?php foreach($ticket->cancelCharges as $charge): ?>
                <?php
                $timestamp = $charge['fromTimestamp'];
                $dayPrint = date('d|n|Y',$timestamp);
                $dateString ='';
                list($dd,$mm,$yy) = explode('|',$dayPrint);
                $dateString ="$dd {$monthNames[$mm]}  {$dayOfWeek[$ww]}";

                ?>
                При отмене после <?php echo $dateString;?> удерживается <?php echo UtilsHelper::formatPrice($charge['price']); ?> RUB.<br>
            <?php endforeach; ?>

        </div>
        <img src="images/vertLine.png" class="hr">
        <?php endif; ?>
        <table class="lastPriceInfo">
            <tr>
                <td class="tdInfo">

                </td>
                <td class="tdPrice">
                    <div class="finishPriceHotel">
                        Итоговая стоимость&nbsp; <span class="price"><?php echo UtilsHelper::formatPrice($ticket->getPrice());?></span> RUB<br>
                        <span class="done">Оплачено полностью</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="lastWords">Желаем вам ярких эмоций и хорошего отдыха</div>
</div>
</body>
</html>
