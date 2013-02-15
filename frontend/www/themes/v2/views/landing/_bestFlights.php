<?php if ($flightCacheFromCurrent): ?>
<div class="headBlockOne">
    <div class="center-block">
        <h2 class="tableH2">Дешевые билеты из <?php echo $currentCity->caseGen;?></h2>
    </div>
    <table class="tableFlight first">
        <thead>
        <tr>
            <td class="tdEmpty">

            </td>
            <td class="tdFlight">
                Рейс
            </td>
            <td class="tdTo">
                Туда
            </td>
            <td class="tdFrom">
                Обратно
            </td>
            <td class="tdPrice">
                Цена
            </td>
        </tr>
        </thead>
        <tbody>
            <?php
            $firstHalf = round(count($flightCacheFromCurrent) / 2);
            $secondHalf = count($flightCacheFromCurrent) - $firstHalf;
            $i = 0;
            foreach ($flightCacheFromCurrent as $fc):
                $i++;
                if ($i <= $firstHalf):
                    $back = ($fc->dateBack == '0000-00-00' ? false : true);
                    ?>
                <tr<?php echo (($i + 1) % 2) == 0 ? ' class="select"' : '';?>>
                    <td class="tdEmpty">

                    </td>
                    <td class="tdFlight">
                        <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span
                            class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?>
                        </div>
                    </td>
                    <td class="tdTo">
                        <?php echo date('d.m', strtotime($fc->dateFrom));?>
                    </td>
                    <td class="tdFrom">
                        <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m', strtotime($fc->dateBack)));?>
                    </td>
                    <td class="tdPrice">
                        <a href="<?php echo '/land/' . City::getCityByPk($fc->to)->country->code . '/' . City::getCityByPk($fc->from)->code . '/' . City::getCityByPk($fc->to)->code . ($fc->dateBack == '0000-00-00' ? '/trip/OW' : '');?>"><span
                            class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span
                            class="rur">o</span></a>
                    </td>
                </tr>
                    <?php
                endif;
            endforeach;?>
        </tbody>
    </table>
    <table class="tableFlight second">
        <thead>
        <tr>

            <td class="tdFlight">
                Рейс
            </td>
            <td class="tdTo">
                Туда
            </td>
            <td class="tdFrom">
                Обратно
            </td>
            <td class="tdPrice">
                Цена
            </td>
            <td class="tdEmpty">

            </td>
        </tr>
        </thead>
        <tbody>
            <?php $i = 0;
            foreach ($flightCacheFromCurrent as $fc):
                $i++;
                if ($i > $firstHalf):
                    $back = ($fc->dateBack == '0000-00-00' ? false : true);?>
                <tr<?php echo ($i % 2) == 0 ? ' class="select"' : '';?>>
                    <td class="tdFlight">
                        <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span
                            class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?>
                        </div>
                    </td>
                    <td class="tdTo">
                        <?php echo date('d.m', strtotime($fc->dateFrom));?>
                    </td>
                    <td class="tdFrom">
                        <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m', strtotime($fc->dateBack)));?>
                    </td>
                    <td class="tdPrice">
                        <a href="<?php echo '/land/' . City::getCityByPk($fc->to)->country->code . '/' . City::getCityByPk($fc->from)->code . '/' . City::getCityByPk($fc->to)->code . ($fc->dateBack == '0000-00-00' ? '/trip/OW' : '');?>"><span
                            class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span
                            class="rur">o</span></a>
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