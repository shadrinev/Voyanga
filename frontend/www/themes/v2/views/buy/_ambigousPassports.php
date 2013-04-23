<?php $flightHeaderPrinted = false; $hotelHeaderPrinted = false; $curEl = -1; $flightCounter = 0; $hotelCounter = 0; $curNumCounter = 0; $curNum = 0; ?>
<div class="oneBlock">
<!--=== ===-->
<div class="paybuyContent">
<table class="infoPassengers">
<?php foreach ($passportForms as $i => $model): ?>
    <?php if (($model instanceof HotelAdultPassportForm) or ($model instanceof HotelChildPassportForm)): ?>
        <?php $prefix = ($model instanceof HotelAdultPassportForm) ? 'FlightAdultPassportForm' : 'FlightChildPassportForm' ?>
        <?php if (!$hotelHeaderPrinted): ?>
            <tr>
                <td colspan="7">
                    <h2><span class="ico-hotel"></span><?php echo $headers['hotels'][$hotelCounter]['common'] ?></h2>
                    <?php $curEl++; ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if ($curNumCounter == 0): ?>
            <tr>
                <td colspan="7">
                    <h3><?php echo $headers['hotels'][$hotelCounter]['rooms'][$curNum]['name'] ?></h3>
                    <?php if (++$curNumCounter > $headers['hotels'][$hotelCounter]['rooms'][$curNum]['counter'])
                    {
                        $curNumCounter = 0;
                        $curNum++;
                    }
                    ?>
                    </h3>
                </td>
            </tr>
        <?php endif ?>
        <script type="text/javascript">
            $(function () {
                $('#syncTranslitFirstName<?php echo $i ?>').syncTranslit({destination: 'syncTranslitFirstName<?php echo $i ?>'});
                $('#syncTranslitLastName<?php echo $i ?>').syncTranslit({destination: 'syncTranslitLastName<?php echo $i ?>'});
            });
        </script>
        <?php if (!$hotelHeaderPrinted): ?>
            <thead>
            <tr>
                <td class="tdName">
                    Имя
                </td>
                <td class="tdLasname">
                    Фамилия
                </td>
                <td class="tdSex">
                    Пол
                </td>
                <td class="tdBirthday">
                </td>
                <td class="tdNationality">
                </td>
                <td class="tdDocumentNumber">
                </td>
                <td class="tdDuration">
                </td>
            </tr>
            </thead>
        <?php endif ?>
        <?php $hotelHeaderPrinted = true; ?>
        <tbody>
        <tr>
            <td class="tdName">
                <?php echo CHtml::textField($prefix.'['.$curEl.']['.$i.'][firstName]','', array('id' => 'syncTranslitFirstName' . $i, 'placeholder' => 'IVAN')); ?>
            </td>
            <td class="tdLastname">
                <?php echo CHtml::textField($prefix.'['.$curEl.']['.$i.'][lastName]','', array('id' => 'syncTranslitFirstName' . $i, 'placeholder' => 'PETROV')); ?>
            </td>
            <td class="tdSex hotel">
                <label class="male" for="male<?php echo $i ?>">
                    <input name="<?php echo $prefix ?>[<?php echo $curEl?>][<?php echo $i ?>][genderId]" type="radio" name="sex" id="male<?php echo $i ?>" value="<?php echo BaseFlightPassportForm::GENDER_MALE?>" <?php if ($model->genderId == BaseFlightPassportForm::GENDER_MALE) echo 'checked="checked"' ?>>
                </label>
                <label class="female" for="female<?php echo $i ?>">
                    <input name="<?php echo $prefix ?>[<?php echo $curEl?>][<?php echo $i ?>][genderId]" type="radio" name="sex" id="female<?php echo $i ?>" value="<?php echo BaseFlightPassportForm::GENDER_FEMALE?>" <?php if ($model->genderId == BaseFlightPassportForm::GENDER_FEMALE) echo 'checked="checked"' ?>>
                </label>
            </td>
            <td class="tdBirthday">
                <?php echo CHtml::hiddenField($prefix.'['.$curEl.']['.$i.'][birthdayDay]', '01'); ?>
                <?php echo CHtml::hiddenField($prefix.'['.$curEl.']['.$i.'][birthdayMonth]', '01'); ?>
                <?php echo CHtml::hiddenField($prefix.'['.$curEl.']['.$i.'][birthdayYear]', '1980'); ?>
            </td>
            <td class="tdNationality">
                <input type='hidden' name="<?php echo $prefix.'['.$curEl.']['.$i.'][countryId]' ?>" value="174">
            </td>
            <td class="tdDocumentNumber">
                <?php echo CHtml::hiddenField($prefix.'['.$curEl.']['.$i.'][seriesNumber]', '123'); ?>
            </td>
            <td class="tdDuration">
                <input type="hidden" value="1" name="<?php echo $prefix.'['.$curEl.']['.$i.'][srok]' ?>">
            </td>
        </tr>
        </tbody>
    <?php endif ?>
    <?php if (($model instanceof FlightAdultPassportForm) or ($model instanceof FlightChildPassportForm) or ($model instanceof FlightInfantPassportForm)): ?>
        <?php if (!$flightHeaderPrinted): ?>
            <thead>
            <tr>
                <td colspan="7" class="paybuyContent"><br>
                    <h3>2. Введите данные пассажиров</h3>
                    <h2><span class="ico-fly"></span><?php echo $headers['flights'][$flightCounter++]; ?></h2>
                    <?php $curEl++; ?>
                </td>
            </tr>
        <?php endif ?>
        <?php if (!$flightHeaderPrinted): ?>
            <tr>
                <td class="tdName">
                    Имя
                </td>
                <td class="tdLasname">
                    Фамилия
                </td>
                <td class="tdSex">
                    Пол
                </td>
                <td class="tdBirthday">
                    <span class="tooltipClose"
                          rel="Введите дату рождения пассажира в формате ДД/ММ/ГГГГ, например 05/02/1985">Дата рождения</span>
                </td>
                <td class="tdNationality">
                    Гражданство
                </td>
                <td class="tdDocumentNumber">
                    <span class="tooltipClose"
                          rel="Для перелетов по России необходим российский или загранпаспорт (для детей — свидетельство о рождении или загранпаспорт). Для зарубежных перелетов необходим загранпаспорт.">Серия и № документа</span>
                </td>
                <td class="tdDuration">
                    <span class="tooltipClose"
                          rel="Срок дейсвия документа необходимо заполнять в случае, если вы указываете в качестве типа документа загранпаспорт.">Срок действия</span>
                </td>
            </tr>
            </thead>
        <?php endif ?>
        <?php $flightHeaderPrinted = true; ?>
        <tbody>
        <tr>
            <td class="tdName">
                <?php echo CHtml::activeTextField($model, "[$curEl][$i]firstName", array('id' => 'syncTranslitFirstName' . $i)); ?>
            </td>
            <td class="tdLastname">
                <?php echo CHtml::activeTextField($model, "[$curEl][$i]lastName", array('id' => 'syncTranslitLastName' . $i)); ?>
            </td>
            <td class="tdSex">
                <label class="male" for="male<?php echo $i ?>">
                    <input name="FlightAdultPassportForm[<?php echo $curEl ?>][<?php echo $i ?>][genderId]" type="radio" name="sex" id="male<?php echo $i ?>" value="<?php echo BaseFlightPassportForm::GENDER_MALE?>" <?php if ($model->genderId == BaseFlightPassportForm::GENDER_MALE) echo 'checked="checked"' ?>>
                </label>
                <label class="female" for="female<?php echo $i ?>">
                    <input name="FlightAdultPassportForm[<?php echo $curEl ?>][<?php echo $i ?>][genderId]" type="radio" name="sex" id="female<?php echo $i ?>" value="<?php echo BaseFlightPassportForm::GENDER_FEMALE?>" <?php if ($model->genderId == BaseFlightPassportForm::GENDER_FEMALE) echo 'checked="checked"' ?>>
                </label>
            </td>
            <td class="tdBirthday">
                <div class="divInputBirthday">
                    <?php echo CHtml::activeTextField($model, "[$curEl][$i]birthdayDay", array(
                        "placeholder" => "ДД",
                        "class" => "dd next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$curEl][$i]birthdayMonth", array(
                        "placeholder" => "ММ",
                        "class" => "mm next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$curEl][$i]birthdayYear", array(
                        "placeholder" => "ГГГГ",
                        "class" => "yy",
                        "maxlength" => "4"
                    )); ?>
                </div>
            </td>
            <td class="tdNationality">
                <?php echo CHtml::activeDropDownList(
                    $model,
                    "[$curEl][$i]countryId",
                    Country::model()->findAllOrderedByPopularity(),
                    array(
                        'data-placeholder' => "Страна...",
                        'class' => "chzn-select",
                        'style' => "width:120px;"
                    )
                ); ?>
            </td>
            <td class="tdDocumentNumber">
                <?php echo CHtml::activeTextField($model, "[$curEl][$i]seriesNumber", array("placeholder" => "4008123456")); ?>
            </td>
            <td class="tdDuration">
                <div class="divInputBirthday">
                    <?php echo CHtml::activeTextField($model, "[$curEl][$i]expirationDay", array(
                        "placeholder" => "ДД",
                        "class" => "dd next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$curEl][$i]expirationMonth", array(
                        "placeholder" => "ММ",
                        "class" => "mm next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$curEl][$i]expirationYear", array(
                        "placeholder" => "ГГГГ",
                        "class" => "yy",
                        "maxlength" => "4"
                    )); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="tdName">
                <input type="checkbox" data-bind="checkbox:{label: 'Есть бонусная карта<?php echo ''.($alliance ? ' '.$alliance->name.' альянса' :' авиакомпании');?>', checked: 0}">
            </td>
            <td class="tdLastname">

            </td>
            <td class="tdSex">

            </td>
            <td class="tdBirthday">

            </td>
            <td class="tdNationality">

            </td>
            <td class="tdDocumentNumber">

            </td>
            <td class="tdDuration">
                <input type="hidden" value="0"
                       name="FlightAdultPassportForm[<?php echo $curEl ?>][<?php echo $i;?>][srok]">
                <input type="checkbox" data-bind="checkbox:{label: 'Без срока', checked: 1}" checked="checked"
                       name="FlightAdultPassportForm[<?php echo $curEl ?>][<?php echo $i;?>][srok]" id="srok<?php echo $i;?>">
            </td>
        </tr>
        <tr>
            <td class="tdBonus" colspan="7">
                <?php if($valAirline): ?>
                <?php $model->bonusCardAirlineCode = $valAirline->code;?>
                <div class="breakDownMenu">
                    <?php echo CHtml::activeDropDownList(
                    $model,
                    "[$curEl][$i]bonusCardAirlineCode",
                    $allianceAirlines,
                    array(
                        'data-placeholder' => "Авиакомпания...",
                        'class' => "chzn-select",
                        'style' => "width:207px;",
                        'disabled' => (count($allianceAirlines) > 1 ? '' : 'disabled'),
                        'value' => $valAirline->code
                    )
                ); ?>
                </div>
                <div class="inputBonus">
                    <input type="text" placeholder="Номер карты" name="<?php echo get_class($model) ?>[<?php echo $i;?>][bonusCard]" >
                </div>
                <?php endif; ?>
            </td>
        </tr>
        </tbody>
    <?php endif ?>
<?php endforeach ?>
</table>
</div>
<!--=== ===-->
</div>
