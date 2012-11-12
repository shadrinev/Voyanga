подробная информация о событии
<?php //print_r($event);?>
<div><?php echo $event->title;?></div>
<div><?php echo $event->address;?></div>
<div><?php echo $event->contact;?></div>
<div><?php echo $event->status;?></div>
<div><?php echo $event->preview;?></div>
<div><?php echo $event->description;?></div>
<div><?php //echo $event->pictureBig;?></div>
<div><?php print_r($event->pictures);?></div>
<div><?php //print_r($event->categories);?></div>
    <?php if($event->links): ?>
        <?php foreach($event->links as $link): ?>
            <div><?php print_r($link->url);?></div>
            <div><?php print_r($link->title);?></div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php if($event->prices): ?>
<?php foreach($event->prices as $price): ?>
    <div><?php print_r($price->getJsonObject());?></div>
    <?php endforeach; ?>
<?php endif; ?>
<div><?php print_r(isset($event->pictureBig) ? $event->imgSrc.$event->pictureBig->url : $event->defaultBigImageUrl);?></div>
<div><?php echo $event->title;?></div>
<div><?php echo $event->title;?></div>
<div><?php echo $event->title;?></div>


