<div class="oneBlock">
    <!--=== ===-->
    <div class="paybuyContent">
        <h2><span class="ico-fly"></span>Ввод данных</h2>
        <h3>Данные пассажиров</h3>
        <table class="infoPassengers">
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
                    <span class="tooltipClose" rel="Для полетов внутри России подходит российский паспорт или загранпаспорт (для детей и младенцев — свидетельство о рождении или загранпаспорт). Для полетов за рубежом нужен загранпаспорт. Обратите внимание, что помимо загранпаспорта, для въезда во многие страны требуется соответствующая виза.">Серия и № документа</span>
                </td>
                <td class="tdDuration">
                    <span class="tooltipClose" rel="Если вы путешествуете с российским паспортом или свидетельством о рождении, то срок действия указывать не нужно, так как эти документы его не имеют. В загранпаспорте же проставлена дата окончания его действия — ее необходимо указать.">Срок действия</span>
                </td>
            </tr>
            </thead>
            <tbody>
            <?php foreach($passportForms as $i=>$model):?>
                <tr>
                    <td class="tdName">
                        <?php echo CHtml::activeTextField($model, "[$i]firstName"); ?>
                    </td>
                    <td class="tdLastname">
                        <?php echo CHtml::activeTextField($model, "[$i]lastName"); ?>
                    </td>
                    <td class="tdSex">
                        <?php echo CHtml::activeHiddenField($model, "[$i]genderId", array('class'=>'genderField')); ?>
                        <div class="gender gender-<?php echo BaseFlightPassportForm::GENDER_MALE?> male" data-value="<?php echo BaseFlightPassportForm::GENDER_MALE?>"></div>
                        <div class="gender gender-<?php echo BaseFlightPassportForm::GENDER_FEMALE?> female" data-value="<?php echo BaseFlightPassportForm::GENDER_FEMALE?>"></div>
                    </td>
                    <td class="tdBirthday">
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
                    </td>
                    <td class="tdNationality">
                        <?php echo CHtml::activeDropDownList(
                            $model,
                            "[$i]countryId",
                            Country::model()->findAllOrderedByPopularity(),
                            array(
                                'data-placeholder'=> "Страна...",
                                'prompt'=> "",
                                'class' => "chzn-select",
                                'style' => "width:140px;"
                            )
                    ); ?>
                    </td>
                    <td class="tdDocumentNumber">
                        <?php echo CHtml::activeTextField($model, "[$i]seriesNumber"); ?>
                    </td>
                    <td class="tdDuration">
                        <?php echo CHtml::activeTextField($model, "[$i]expirationDay", array(
                            "placeholder" => "ДД",
                            "class" => "dd next",
                            "maxlength" => "2"
                        )); ?>
                        <?php echo CHtml::activeTextField($model, "[$i]expirationMonth", array(
                            "placeholder" => "ММ",
                            "class" => "mm next",
                            "maxlength" => "2"
                        )); ?>
                        <?php echo CHtml::activeTextField($model, "[$i]expirationYear", array(
                            "placeholder" => "ГГГГ",
                            "class" => "yy",
                            "maxlength" => "4"
                        )); ?>
                    </td>
                </tr>
                <tr>
                    <td class="tdName">
                        <input type="checkbox" name="bonus" id="bonus">
                        <label for="bonus">Есть бонусная карта</label>
                    </td>
                    <td class="tdLastname"></td>
                    <td class="tdSex"></td>
                    <td class="tdBirthday"></td>
                    <td class="tdNationality"></td>
                    <td class="tdDocumentNumber"></td>
                    <td class="tdDuration">
                        <input type="checkbox" checked="checked" name="srok[<?php echo $i;?>]" id="srok<?php echo $i;?>">
                        <label for="srok<?php echo $i;?>">Без срока</label>
                    </td>
                </tr>
            <?php endforeach; ?>
            <!-- NEW USER -->
            </tbody>
        </table>
    </div>
    <!--=== ===-->
</div>