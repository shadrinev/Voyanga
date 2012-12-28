<?php
    $dayOfWeek = array('Вс','Пн','Вт','Ср','Чт','Пт','Сб');
    $monthNames = array('','янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек');

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
                    <td class="tdEticket">Электронный билет<br>
                        E-ticket</td>
                    <td style="width: 215px; font-size: 18px;" align="center">+7 (499) 533-09-33</td>
                    <td class="tdLink"><a href="http://www.voyanga.com">www.voyanga.com</a><br>
                        <a href="mailto:support@voyanga.com">support@voyanga.com</a></td>
                </tr>
            </table>
            <table class="hederInfo">
                <thead>
                <tr>
                    <td class="tdNumberVoyanga">
                        Номер заказа<br>
                        Order Number
                    </td>
                    <td class="tdNumberBooking">
                        Номер бронирования<br>
                        Booking Number
                    </td>
                    <td class="tdReserve">
                        Номер билета<br>
                        Ticket number
                    </td>
                    <td class="tdInfoPassenger">
                        Сведения о пассажирах<br>
                        Traveler Information
                    </td>
                </tr>
                </thead>
                <tbody>
                <?php foreach($flightPassports as $pKey=>$passport):?>
                    <tr>
                        <td class="tdNumberVoyanga">
                            <?php if($pKey===0): ?>
                                <strong><?php echo $bookingId; ?></strong>
                            <?php endif; ?>
                        </td>
                        <td class="tdNumberBooking">
                            <?php if($pKey===0): ?>
                            <strong><?php echo $pnr; ?></strong>
                            <?php endif; ?>
                        </td>
                        <td class="tdReserve">
                            <strong><?php echo $passport->ticketNumber; ?></strong>
                        </td>
                        <td class="tdInfoPassenger">
                            <div class="passenger"><?php echo strtoupper($passport->lastName.' '.$passport->firstName.' '.($passport->genderId == Passport::GENDER_M ? 'MR' : 'MS')); ?><br>
                                <?php echo $passport->series.$passport->number; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <img src="images/vertLine.png" class="hr">
            <?php //print_r($ticket);?>
            <?php if(count($ticket->flights)!=2):?>
                <h2>Маршрут</h2>
            <?php endif; ?>
            <?php foreach($ticket->flights as $key=>$flight):?>
                <?php if( count($ticket->flights) === 2):?>
                    <h2><?php echo ($key==0 ? "Туда" : "Обратно");?></h2>
                <?php endif; ?>
                <div class="tickets">
                    <?php
                    $firstPart = true;
                    $date = "";
                    $dayFormat = "d|n|w";
                    $lastKey = count($flight->flightParts) - 1;

                    foreach( $flight->flightParts as $keyPart=>$flightPart):
                    ?>
                        <?php if(!$firstPart): ?>
                            <table class="wait">
                                <tr>
                                    <td class="tdDate"></td>
                                    <td class="tdTime"></td>
                                    <td class="tdIco">
                                        <img src="images/ico-cup.png">
                                    </td>
                                    <td class="tdCity">Пересадка: между рейсами <?php echo UtilsHelper::durationToTime($flight->transits[($keyPart - 1)]->timeForTransit);?></td>
                                </tr>
                            </table>
                        <?php endif; ?>
                        <?php
                        $dayPrint = date($dayFormat,$flightPart->timestampBegin);
                        $dateString ='';
                        if($dayPrint!=$date){
                            $date = $dayPrint;
                            list($dd,$mm,$ww) = explode('|',$dayPrint);
                            $dateString ="<span class='f16'>$dd</span> {$monthNames[$mm]}, <span class='weekDay'>{$dayOfWeek[$ww]}</span>";
                        }
                        ?>
                        <table class="fly<?php echo ($keyPart==0 ? " start" : '').($keyPart==$lastKey ? " end" : "");?>">
                            <tr class="startFly">
                                <td class="tdDate"<?php echo ($dateString ? '' : ' style="border-top:1px solid #fff"');?>><?php echo $dateString;?></td>
                                <td class="tdTime"><?php echo date('H:i',$flightPart->timestampBegin);?></td>
                                <td class="tdIco">
                                    <?php if($keyPart==0): ?>
                                        <img src="images/ico-fly.png">
                                    <?php endif; ?>
                                </td>
                                <td class="tdCity"><?php echo City::getCityByPk($flightPart->departureCityId)->localRu;?>, <span class="airport"><?php echo $flightPart->departureAirport->localRu?><?php echo ($flightPart->departureTerminalCode ? " ({$flightPart->departureTerminalCode})" : '');?></span></td>
                                <td rowspan="3" class="tdAvia">
                                    <img src="img/airline_logos/<?php echo $flightPart->transportAirlineCode;?>.png"><span class="airlineName"><?php echo Airline::getAirlineByCode($flightPart->transportAirlineCode)->localRu;?></span><br>
                                    Рейс: <?php echo $flightPart->transportAirlineCode.' '.$flightPart->code;?><br>

                                </td>
                                <td rowspan="3" class="tdTarif">
                                    <?php if(implode(',',$flightPart->bookingCodes)):?>
                                    Тариф: <?php echo implode(',',$flightPart->bookingCodes);?>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr class="timeFly">
                                <td class="tdDate"></td>
                                <td class="tdTime"></td>
                                <td class="tdIco"></td>
                                <td class="tdCity">Перелет продлится <?php echo UtilsHelper::durationToTime($flightPart->duration);?></td>
                            </tr>
                            <?php
                            $dayPrint = date($dayFormat,$flightPart->timestampBegin);
                            $dateString ='';
                            if($dayPrint!=$date){
                                $date = $dayPrint;
                                list($dd,$mm,$ww) = explode('|',$dayPrint);
                                $dateString ="<span class='f16'>$dd</span> {$monthNames[$mm]}, <span class='weekDay'>{$dayOfWeek[$ww]}</span>";
                            }
                            ?>
                            <tr class="endFly">
                                <td class="tdDate"<?php echo ($dateString ? '' : ' style="border-top:1px solid #fff"');?>><?php echo $dateString;?></td>
                                <td class="tdTime"><?php echo date('H:i',$flightPart->timestampEnd);?></td>
                                <td class="tdIco">
                                    <?php if($keyPart==$lastKey): ?>
                                        <img src="images/ico-fly.png">
                                    <?php endif; ?>
                                </td>
                                <td class="tdCity"><?php echo City::getCityByPk($flightPart->arrivalCityId)->localRu;?>, <span class="airport"><?php echo $flightPart->arrivalAirport->localRu?><?php echo ($flightPart->arrivalTerminalCode ? " ({$flightPart->arrivalTerminalCode})" : '');?></span></td>
                            </tr>
                        </table>
                        <?php $firstPart = false; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach;?>

            <img src="images/vertLine.png" class="hr">
            <table class="lastPriceInfo">
                <tr>
                    <td class="tdInfo">
                        Билет <?php echo ($ticket->refundable ? "возвратный" : "невозвратный");?>.<br>
                        <br>
                        <?php echo $ticket->valAirline->economDescription;?><br>
                        <br>
                        Время отправления и прибытия местное.
                    </td>
                    <td class="tdPrice">
                        <div class="finishPrice">
                            <table>
                                <tr>
                                    <td class="tdTitle">Итоговая стоимость</td>
                                    <td class="tdPrice"><span><?php echo UtilsHelper::formatPrice($ticket->price);?></span> RUB</td>
                                </tr>
                            </table>
                        </div>

                        <h3> Сведения об оплате / Payment Information</h3>

                        <table class="tableLastInfo">
                            <tr>
                                <td class="tdTitle">Тариф / Fare</td>
                                <td class="tdPrice"><?php echo UtilsHelper::formatPrice($ticket->baseFare);?> RUB</td>
                            </tr>
                            <tr>
                                <td class="tdTitle">Таксы и сборы / Taxes and fees</td>
                                <td class="tdPrice"><?php echo UtilsHelper::formatPrice($ticket->taxes);?> RUB</td>
                            </tr>
                            <tr>
                                <td class="tdTitle">Коммисия / Agency Fee</td>
                                <td class="tdPrice"><?php echo UtilsHelper::formatPrice($ticket->commission);?> RUB</td>
                            </tr>
                            <tr>
                                <td class="tdTitle">Форма оплаты / Paid by Invoice</td>
                                <td class="tdPrice">Card</td>
                            </tr>
                            <tr>
                                <td class="tdTitle">&nbsp;</td>
                                <td class="tdPrice">&nbsp;</td>
                            </tr>
                            <tr>

                            </tr>
                        </table>
                    </td>


                </tr>
            </table>

        </div>
        <div class="lastWords">Желаем вам приятного полёта</div>
    </div>
</body>
</html>