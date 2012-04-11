Eto flight voyages Stack
<?php echo $this->data;?>

<?php foreach ($this->data as $oFlightVoyageStack):?>
	<?php $oFlightVoyage = each($oFlightVoyageStack->aFlightVoyages);?>
	<div>
	Цена билета: <?php echo $oFlightVoyage['value']->price;?>
	ID:<?php echo $oFlightVoyage['value']->flight_key;?>
	<?php foreach ($oFlightVoyage['value']->aFlights as $oFlight):?>
		<div>
		<?php echo $oFlight->departure_city->local_ru;?> -- <?php echo $oFlight->arrival_city->local_ru;?>
			<?php if($oFlight->aTransits): ?>
				<?php echo count($oFlight->aTransits);?> пересадка(и)
				<?php if($oFlightVoyage['value']->flight_key == '-172'){print_r($oFlight->aTransits);}?>
			<?php endif;?>
		</div>
		<?php ?>
	<?php endforeach;?>
	
	</div>
<?php endforeach;?>