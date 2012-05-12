Eto flight voyages Stack
<?php echo $this->data;?>

<?php foreach ($this->data as $oFlightVoyageStack):?>
	<?php $oFlightVoyage = each($oFlightVoyageStack->aFlightVoyages);?>
	<div>
	Цена билета: <?php echo $oFlightVoyage['value']->price;?>
	ID:<?php echo $oFlightVoyage['value']->flightKey;?>
	<?php foreach ($oFlightVoyage['value']->flights as $oFlight):?>
		<div>

            <?php
                // print_r($oFlight);
                /*if(isset($oFlight->departureCity)){
                    echo "departureCity set";
                    if(isset($oFlight->departureCity->localRu)){
                        echo $oFlight->departureCity->localRu;
                    }else{
                        echo "localRu not set";
                    }
                }else{
                    echo "departureCity not set";
                }*/

             ?>
		    <?php echo $oFlight->departureCity->localRu;?> -- <?php echo $oFlight->arrivalCity->localRu;?>
			<?php if($oFlight->transits): ?>
				<?php echo count($oFlight->transits);?> пересадка(и)
				<?php if($oFlightVoyage['value']->flightKey == '-172'){print_r($oFlight->transits);}?>
			<?php endif;?>
            <?php /**/?>
		</div>
		<?php ?>
	<?php endforeach;?>
	
	</div>
<?php endforeach;?>