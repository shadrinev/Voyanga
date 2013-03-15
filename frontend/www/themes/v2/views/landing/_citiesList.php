<?php
$firstHalf = ceil(count($cities)/2);
if($cities):
?>
<div class="headBlockOne">
    <div class="center-block">
        <h2 class="tableH2"><?php echo $title;?></h2>
    </div>
<table class="tableFlight first up">
    <tbody>
    <?php
    $i =0;
    foreach($cities as $city):
        $i++;
        if($i <= $firstHalf):
            ?>
        <tr class="<?php echo (($i+1) % 2 == 0)? 'select' : '' ;?>">
            <td class="tdEmpty">

            </td>
            <td class="tdFlight">
                <div><?php echo $city->localRu;?></div>
            </td>
            <? if($isHotels):?>
            <td class="tdHostel">
                <a href="/land/hotels/<?php echo $city->country->code.'/'.$city->code;?>">отели</a>
            </td>
            <? else: ?>
            <td class="tdFly">
                <a href="/land/<?php echo $city->country->code.'/'.$city->code;?>">перелеты</a>
            </td>
            <? endif;?>
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
    foreach($cities as $city):
        $i++;
        if($i > $firstHalf):
            ?>
        <tr class="<?php echo (($i+$firstHalf+1) % 2 == 0)? 'select' : '' ;?>">

            <td class="tdFlight">
                <div><?php echo $city->localRu;?></div>
            </td>
            <? if($isHotels):?>
            <td class="tdHostel">
                <a href="/land/hotels/<?php echo $city->country->code.'/'.$city->code;?>">отели</a>
            </td>
            <? else: ?>
            <td class="tdFly">
                <a href="/land/<?php echo $city->country->code.'/'.$city->code;?>">перелеты</a>
            </td>
            <? endif;?>
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
<?php endif; ?>