<div class="oneBlock">
<!--=== ===-->
<div class="paybuyContent" id="tableStartRun">
<h2><span class="<?php echo $icon ?>"></span><?php echo $header ?></h2>
<table class="infoPassengers">
<thead>
<?php $currentRoomId = 0; $currentRoomAdults = 0; $currentRoomChild = 0; ?>
<?php if ($roomCounters): ?>
<tr>
    <td colspan="7"><h3>Номер <?php echo $currentRoomId + 1 ?>
            : <?php echo $roomCounters[$currentRoomId]['label'] ?></h3></td>
</tr>
<tr>
    <td class="tdName">
        Имя
    </td>
    <td class="tdLasname">
        Фамилия
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
    </td>
</tr>
</thead>
<?php else: ?>
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
            Дата рождения
        </td>
        <td class="tdNationality">
            Гражданство
        </td>
        <td class="tdDocumentNumber">
                    <span class="tooltipClose"
                          rel="Для полетов внутри России подходит российский паспорт или загранпаспорт (для детей и младенцев — свидетельство о рождении или загранпаспорт). Для полетов за рубежом нужен загранпаспорт. Обратите внимание, что помимо загранпаспорта, для въезда во многие страны требуется соответствующая виза.">Серия и № документа</span>
        </td>
        <td class="tdDuration">
                    <span class="tooltipClose"
                          rel="Если вы путешествуете с российским паспортом или свидетельством о рождении, то срок действия указывать не нужно, так как эти документы его не имеют. В загранпаспорте же проставлена дата окончания его действия — ее необходимо указать.">Срок действия</span>
        </td>
    </tr>
    </thead>
<?php endif ?>
<tbody>
<?php foreach ($passportForms as $i => $model): ?>
    <?php
    if ($roomCounters) {
        if (($roomCounters[$currentRoomId]['adult'] == $currentRoomAdults) and ($roomCounters[$currentRoomId]['child'] == $currentRoomChild)) {
            $currentRoomId++;
            $currentRoomAdults = 0;
        } elseif ($model instanceof FlightAdultPassportForm)
            $currentRoomAdults++; elseif ($model instanceof FlightChildPassportForm)
            $currentRoomChild++;
    }
    ?>
    <?php if ($roomCounters and ($currentRoomAdults == 0)): ?>
        <tr>
            <td colspan="7"><h3>Номер <?php echo $currentRoomId + 1 ?>
                    (<?php echo $roomCounters[$currentRoomId]['label'] ?>)</h3></td>
        </tr>
    <?php endif ?>
    <script type="text/javascript">
        $(function () {
            $('#syncTranslitFirstName<?php echo $i ?>').syncTranslit({destination: 'syncTranslitFirstName<?php echo $i ?>'});
            $('#syncTranslitLastName<?php echo $i ?>').syncTranslit({destination: 'syncTranslitLastName<?php echo $i ?>'});
        });
    </script>
    <tr>
        <td class="tdName">
            <?php echo CHtml::activeTextField($model, "[$i]firstName", array('id' => 'syncTranslitFirstName' . $i, 'placeholder' => 'Иван')); ?>
        </td>
        <td class="tdLastname">
            <?php echo CHtml::activeTextField($model, "[$i]lastName", array('id' => 'syncTranslitLastName' . $i, 'placeholder' => 'Петров')); ?>
        </td>
        <td class="tdSex">
            <?php if (!$roomCounters): ?>
                <label class="male <?php if ($hide) echo 'inactive' ?>" for="male<?php echo $i ?>">
                    <input type="radio" name="<?php echo get_class($model) ?>[<?php echo $i ?>][genderId]" id="male<?php echo $i ?>"
                           value="<?php echo BaseFlightPassportForm::GENDER_MALE?>"
                        <?php if ($model->genderId == BaseFlightPassportForm::GENDER_MALE) echo 'checked="checked"' ?>>
                </label>
                <label class="female <?php if ($hide) echo 'inactive' ?>" for="female<?php echo $i ?>">
                    <input type="radio" name="<?php echo get_class($model) ?>[<?php echo $i ?>][genderId]"
                           id="female<?php echo $i ?>"
                           value="<?php echo BaseFlightPassportForm::GENDER_FEMALE?>"
                        <?php if ($model->genderId == BaseFlightPassportForm::GENDER_FEMALE) echo 'checked="checked"' ?>>
                </label>
            <?php else: ?>
                <input type="hidden" name="<?php echo get_class($model) ?>[<?php echo $i ?>][genderId]"
                       value="<?php echo BaseFlightPassportForm::GENDER_MALE?>">
            <?php endif ?>
        </td>
        <td class="tdBirthday">
            <?php if (!$roomCounters): ?>
                <div class="divInputBirthday <?php if ($hide) echo 'active' ?>">
                    <?php echo CHtml::activeTextField($model, "[$i]birthdayDay", array(
                        "placeholder" => "ДД",
                        "class" => "dd next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$i]birthdayMonth", array(
                        "placeholder" => "ММ",
                        "class" => "mm next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$i]birthdayYear", array(
                        "placeholder" => "ГГГГ",
                        "class" => "yy",
                        "maxlength" => "4"
                    )); ?>
                </div>
            <?php else: ?>
                <?php echo CHtml::activeHiddenField($model, "[$i]birthdayDay", array('value'=>'01')); ?>
                <?php echo CHtml::activeHiddenField($model, "[$i]birthdayMonth", array('value'=>'01')); ?>
                <?php echo CHtml::activeHiddenField($model, "[$i]birthdayYear", array('value'=>'1980')); ?>
            <?php endif ?>
        </td>
        <td class="tdNationality <?php if ($hide) echo "inactive" ?>">
            <?php if (!$roomCounters): ?>
                <?php if ($hide): ?>
                    <input type='text' disabled="disabled"
                           value="<?php $c = Country::model()->findByPk($model->countryId); echo CHtml::value($c, 'localRu') ?>">
                <?php else: ?>
                    <?php echo CHtml::activeDropDownList(
                        $model,
                        "[$i]countryId",
                        Country::model()->findAllOrderedByPopularity(),
                        array(
                            'data-placeholder' => "Страна...",
                            'class' => "chzn-select",
                            'style' => "width:120px;",
                        )
                    ); ?>
                <?php endif ?>
            <?php else: ?>
                <input type='hidden' name="<?php echo get_class($model)."[$i][countryId]" ?>" value="174">
            <?php endif ?>
        </td>
        <td class="tdDocumentNumber">
            <?php if (!$roomCounters): ?>
                <?php echo CHtml::activeTextField($model, "[$i]seriesNumber"); ?>
            <?php else: ?>
                <?php echo CHtml::activeHiddenField($model, "[$i]seriesNumber", array('value'=>'-')); ?>
            <?php endif ?>
        </td>
        <td class="tdDuration">
            <?php if (!$roomCounters): ?>
                <div class="divInputBirthday checkOn <?php if ($hide) echo 'active' ?>">
                    <?php echo CHtml::activeTextField($model, "[$i]expirationDay", array(
                        "placeholder" => "ДД",
                        "class" => "expiration dd next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$i]expirationMonth", array(
                        "placeholder" => "ММ",
                        "class" => "expiration mm next",
                        "maxlength" => "2"
                    )); ?>
                    <?php echo CHtml::activeTextField($model, "[$i]expirationYear", array(
                        "placeholder" => "ГГГГ",
                        "class" => "expiration yy",
                        "maxlength" => "4"
                    )); ?>
                </div>
            <?php endif; ?>
        </td>
    </tr>
    <?php if ((!$hide) && (!$roomCounters)): ?>
        <tr class="trDurationPadding">
            <td class="tdName">
                <!--<input type="checkbox" data-bind="checkbox:{label: 'Есть бонусная карта', checked: 0}" checked="checked" name="srok[<?php echo $i;?>]" id="srok<?php echo $i;?>">-->
            </td>
            <td class="tdLastname"></td>
            <td class="tdSex"></td>
            <td class="tdBirthday"></td>
            <td class="tdNationality"></td>
            <td class="tdDocumentNumber"></td>
            <td class="tdDuration">
                <input type="hidden" value="0"
                       name="<?php echo get_class($model) ?>[<?php echo $i;?>][srok]">
                <input type="checkbox" data-bind="checkbox:{label: 'Без срока', checked: 0}"
                       name="<?php echo get_class($model) ?>[<?php echo $i;?>][srok]" id="srok<?php echo $i;?>">
            </td>
        </tr>
    <?php else: ?>
        <input type="hidden" value="1" name="<?php echo get_class($model) ?>[<?php echo $i;?>][srok]">
    <?php endif ?>
<?php endforeach; ?>
<!-- NEW USER -->
</tbody>
</table>
</div>
<!--=== ===-->
</div>