<p>Опять нас <strike>наеб</strike> обманывают ГДС'ки!</p>
<p>Ценники после бронирования не совпадают</p>
<p>
    Было:
    <pre>
        <?php print_r($oldInfo);?>
    </pre>
    Стало:
    <pre>
        <?php print_r($newInfo);?>
    </pre>
    Билет:
    <?php echo $voyage->getDepartureDate()." ".$voyage->getDepartureCity()->code."-&gt;".$voyage->getArrivalCity()->code.
    ($voyage->isRoundTrip() ? " ".$voyage->getDepartureDate(1)." ".$voyage->getDepartureCity(1)->code."-&gt;".$voyage->getArrivalCity(1)->code : '').
    $voyage->getTransportAirlines().' '.implode(',',$voyage->getFlightCodes());?>
</p>
<p>Спасибо!</p>
