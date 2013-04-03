<?php if ($hotelsInfo): ?>
<div class="headBlockTwo">
    <div class="center-block">
        <?php if(!isset($hotelsUrl)) $hotelsUrl = false;?>
        <?php if($hotelsUrl && ($city->hotelbookId > 0)):?>
        <h2><a href="/land/hotels/<?php echo $city->country->code.'/'.$city->code;?>">Отели в <?php echo $city->casePre;?></a></h2>
        <?php else:?>
        <h2>Отели в <?php echo $city->casePre;?></h2>
        <?php endif;?>

        <?php foreach ($hotelsInfo as $hotInfo): ?>
        <div class="hotels-tickets parkPage">
            <div class="content">
                <div class="full-info">
                    <div class="preview-photo">
                        <ul>
                            <li><a class="photo" href="<?php echo '/land/hotel/' . $hotInfo->hotelId;?>"><img
                                src="<?php echo $hotInfo->getFrontImageUrl();?>"></a></li>
                        </ul>
                    </div>
                    <div class="stars <?php echo $hotInfo->getWordStars();?>"></div>
                    <div class="overflowBlock">
                        <h4><a
                            href="<?php echo '/land/hotel/' . $hotInfo->hotelId;?>"><?php echo $hotInfo->hotelName;?></a>
                        </h4>

                        <div class="street">
                            <span><?php echo $hotInfo->address;?></span>
                            <span class="gradient"></span>
                        </div>
                    </div>
                    <div class="how-cost">
                        от <span class="cost"><a
                        href="<?php echo '/land/hotel/' . $hotInfo->hotelId;?>"><?php echo UtilsHelper::formatPrice($hotInfo->price);?></a></span>
                        <span class="rur">o</span> / ночь
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
<?php endif; ?>