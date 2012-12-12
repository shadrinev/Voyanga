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
                    <td style="width: 215px; font-size: 18px;">+7 (495) 660-20-20</td>
                    <td class="tdLink"><a href="http://www.voyanga.com">www.voyanga.com</a><br>
                        <a href="mailto:support@voyanga.com">support@voyanga.com</a></td>
                </tr>
            </table>
            <table class="hederInfo">
                <tr>
                    <td class="tdNumberVoyanga">
                        Номер заказа в системе Voyanga<br>
                        Booking number<br>
                        <strong><?php echo $bookingId; ?></strong>
                    </td>
                    <td class="tdReserve">
                        Номер бронирования<br>
                        Booking number<br>
                        <strong><?php echo $pnr.($ticket->webService ? " ({$ticket->webService})":''); ?></strong>
                    </td>
                    <td class="tdInfoPassenger">
                        Сведения о пассажирах<br>
                        Traveler Information<br>
                        <strong>SHADRIN EUGENE MR<br>
                            4704973481</strong>
                    </td>
                </tr>
            </table>
            <hr>
            <?php if(count($ticket->flights)!=2):?>
                <h2>Маршрут</h2>
            <?php endif; ?>
            <?php foreach($ticket->flights as $key=>$flight):?>
                <h2>Туда</h2>
                <?php if(count($ticket->flights)==2):?>
                    <h2><?php echo ($key==0 ? "Туда" : "Обратно");?></h2>
                <?php endif; ?>
                <div class="tickets">
                    <?php
                    $firstPart = true;
                    $date = "";
                    foreach( $flight->flightParts as $keyPart=>$flightPart)
                    ?>
                    <table class="fly start">
                        <tr class="startFly">
                            <td class="tdDate"><span class="f16">25</span> май</td>
                            <td class="tdTime">9:40</td>
                            <td class="tdIco">
                                <img src="images/ico-fly.png">
                            </td>
                            <td class="tdCity">Санкт-Петербург, <span class="airport">Пулково-2</span></td>
                            <td rowspan="3" class="tdAvia">
                                <img src="images/logoAvia.png"><br>
                                Рейс: AZW1545<br>

                            </td>
                            <td rowspan="3" class="tdTarif">
                                Тариф: L (HK)
                            </td>
                        </tr>
                        <tr class="timeFly">
                            <td class="tdDate"></td>
                            <td class="tdTime"></td>
                            <td class="tdIco"></td>
                            <td class="tdCity">Перелет продлится 1 ч. 50 м.</td>
                        </tr>
                        <tr class="waitFly">
                            <td class="tdDate"  style="border-top:1px solid #fff"></td>
                            <td class="tdTime">9:40</td>
                            <td class="tdIco"></td>
                            <td class="tdCity">Нью-Йорк, <span class="airport">NYC-1 Airport</span></td>
                        </tr>
                    </table>
                    <table class="wait">
                        <tr>
                            <td class="tdDate"></td>
                            <td class="tdTime"></td>
                            <td class="tdIco">
                                <img src="images/ico-cup.png">
                            </td>
                            <td class="tdCity">Пересадка: между рейсами 1 ч. 30 м.</td>
                        </tr>
                    </table>
                    <table class="fly middle">
                        <tr class="waitFly start">
                            <td class="tdDate"><span class="f16">25</span> май</td>
                            <td class="tdTime">9:40</td>
                            <td class="tdIco"></td>
                            <td class="tdCity">Санкт-Петербург, <span class="airport">Пулково-2</span></td>
                            <td rowspan="3" class="tdAvia">
                                <img src="images/logoAvia.png"><br>
                                Рейс: AZW1545<br>
                            </td>
                            <td rowspan="3" class="tdTarif">
                                Тариф: L (HK)
                            </td>
                        </tr>
                        <tr class="timeFly">
                            <td class="tdDate"></td>
                            <td class="tdTime"></td>
                            <td class="tdIco"></td>
                            <td class="tdCity">Перелет продлится 1 ч. 50 м.</td>
                        </tr>
                        <tr class="waitFly end">
                            <td class="tdDate"><span class="f16">25</span> май</td>
                            <td class="tdTime">9:40</td>
                            <td class="tdIco"></td>
                            <td class="tdCity">Нью-Йорк, <span class="airport">NYC-1 Airport</span></td>
                        </tr>
                    </table>
                    <table class="wait">
                        <tr>
                            <td class="tdDate"></td>
                            <td class="tdTime"></td>
                            <td class="tdIco"><img src="images/ico-cup.png"></td>
                            <td class="tdCity">Пересадка: между рейсами 1 ч. 30 м.</td>
                        </tr>
                    </table>
                    <table class="fly end">
                        <tr class="waitFly">
                            <td class="tdDate"><span class="f16">25</span> май</td>
                            <td class="tdTime">9:40</td>
                            <td class="tdIco"></td>
                            <td class="tdCity">Санкт-Петербург, <span class="airport">Пулково-2</span></td>
                            <td rowspan="3" class="tdAvia">
                                <img src="images/logoAvia.png"><br>
                                Рейс: AZW1545<br>
                            </td>
                            <td rowspan="3" class="tdTarif">
                                Тариф: L (HK)
                            </td>
                        </tr>
                        <tr class="timeFly">
                            <td class="tdDate"></td>
                            <td class="tdTime"></td>
                            <td class="tdIco"></td>
                            <td class="tdCity">Перелет продлится 1 ч. 50 м.</td>
                        </tr>
                        <tr class="endFly">
                            <td class="tdDate"><span class="f16">25</span> май</td>
                            <td class="tdTime">9:40</td>
                            <td class="tdIco"><img src="images/ico-fly.png"></td>
                            <td class="tdCity">Нью-Йорк, <span class="airport">NYC-1 Airport</span></td>
                        </tr>
                    </table>
                </div>
            <?php endforeach;?>
            <hr>
            <table class="lastPriceInfo">
                <tr>
                    <td class="tdPrice">
                        <div class="f13"> Сведения об оплате / Payment Information</div>
                        <br>

                        <table class="tableLastInfo">
                            <tr>
                                <td class="tdTitle">Тариф / Fare</td>
                                <td class="tdPrice">8150,00 RUB</td>
                            </tr>
                            <tr>
                                <td class="tdTitle">Таксы и сборы / Taxes and fees</td>
                                <td class="tdPrice">3277,33 RUB</td>
                            </tr>
                            <tr>
                                <td class="tdTitle">Коммисия / Agency Fee</td>
                                <td class="tdPrice">334,15 RUB</td>
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
                                <td class="tdTitle">Итоговая стоимость / Total price</td>
                                <td class="tdPrice"><div class="price"> 63 250 RUB</div></td>
                            </tr>
                        </table>
                    </td>
                    <td class="tdInfo">
                        Билет возвратный.<br>
                        <br>
                        Норма провоза бесплатного багажа у авиакомпании Aigle Azur в экономическом классе: багаж не должен превышать по весу 40 кг и по сумме трех измерений 158 см на человека.<br>
                        <br>
                        Время отправления и прибытия местное.
                    </td>

                </tr>
            </table><br>
            <br>
            <p align="center">Желаем приятного полета</p>
        </div>
    </div>
</body>
</html>