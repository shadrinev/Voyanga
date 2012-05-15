
<?php echo $this->data;?>
<div class="flight-results">
<?php foreach ($this->data as $oFlightVoyageStack):?>
	<?php $oFlightVoyage = each($oFlightVoyageStack->flightVoyages);?>
    <div class="ticket-splitter"><br /></div>
	<div class="ticket">
	    Цена билета: <?php echo $oFlightVoyage['value']->price;?>
	    ID:<?php echo $oFlightVoyage['value']->flightKey;?>
	    <?php foreach ($oFlightVoyage['value']->flights as $flight):?>
	    	<div class="route">
                <?php $flightPart = each($flight->flightParts);?>
                <div class="route-point">
	    	        <div class="route-point-city"><?php echo $flight->departureCity->localRu;?></div>
                    <div class="route-point-airport-code"><?php echo $flightPart['value']->departureAirport->code;?></div>
                    <div class="route-point-time"><?php echo date('d.m.Y H:i',$flightPart['value']->timestampBegin);?></div>
                    <div class="route-point-airport-name"><?php echo $flightPart['value']->departureAirport->localRu;?></div>
                </div>

                <div class="route-flight-info">
                    <div class="route-flight-time"><?php echo UtilsHelper::durationToTime($flightPart['value']->duration);?></div>
                    <div class="route-flight-airline"><?php echo $flightPart['value']->opAirline->localRu;?></div>
                    <div class="route-flight-aircraft"><?php echo $flightPart['value']->aircraftCode;?></div>
                    <div class="route-flight-flight-code"><?php echo $flightPart['value']->code;?></div>
                </div>
                -
                <?php if($flight->transits): ?>
                    <?php foreach($flight->transits as $transit):?>
                        <div class="route-point">
                            <div class="route-point-city"><?php echo $transit->city->localRu;;?></div>
                            <div class="route-point-time"><?php echo UtilsHelper::durationToTime($transit->timeForTransit);?></div>
                        </div>
                        <?php $flightPart = each($flight->flightParts);?>
                        <div class="route-flight-info">
                            <div class="route-flight-time"><?php echo UtilsHelper::durationToTime($flightPart['value']->duration);?></div>
                            <div class="route-flight-airline"><?php echo $flightPart['value']->opAirline->localRu;?></div>
                            <div class="route-flight-aircraft"><?php echo $flightPart['value']->aircraftCode;?></div>
                            <div class="route-flight-flight-code"><?php echo $flightPart['value']->code;?></div>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
                <div class="route-point">
                    <div class="route-point-city"><?php echo $flight->arrivalCity->localRu;?></div>
                    <div class="route-point-airport-code"><?php echo $flightPart['value']->arrivalAirport->code;?></div>
                    <div class="route-point-time"><?php echo date('d.m.Y H:i',$flightPart['value']->timestampEnd);?></div>
                    <div class="route-point-airport-name"><?php echo $flightPart['value']->arrivalAirport->localRu;?></div>
                </div>
		    </div>
	    <?php endforeach;?>
	</div>
<?php endforeach;?>
</div>