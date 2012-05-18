<div class="form">
    <?php echo CHtml::beginForm(); ?>
        <?php //echo CHtml::activeTextField($item,"[$i]name"); ?>
        <div class="row">
            <?php echo CHtml::activeLabel($booking,"contactEmail"); ?>
            <?php echo CHtml::activeTextField($booking,"contactEmail"); ?>
        </div>
        <div class="row">
            <?php echo CHtml::activeLabel($booking,"contactPhone"); ?>
            <?php echo CHtml::activeTextField($booking,"contactPhone"); ?>
        </div>
        <?php foreach($passports as $i=>$passport): ?>
            <?php echo CHtml::openTag('fieldset')."<legend>Пасспортные данные для ".($i+1)."-го пассажира</legend>\n" ;?>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]firstName"); ?>
                <?php echo CHtml::activeTextField($passport,"[$i]firstName"); ?>
            </div>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]lastName"); ?>
                <?php echo CHtml::activeTextField($passport,"[$i]lastName"); ?>
            </div>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]number"); ?>
                <?php echo CHtml::activeTextField($passport,"[$i]number"); ?>
            </div>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]series"); ?>
                <?php echo CHtml::activeTextField($passport,"[$i]series"); ?>
            </div>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]birthday"); ?>
                <?php echo CHtml::activeTextField($passport,"[$i]birthday"); ?>
            </div>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]documentTypeId"); ?>
                <?php echo CHtml::activeDropDownList($passport,"[$i]documentTypeId",array(1=>'Пасспорт РФ',2=>'Загран паспорт', 3=>'св-во о рожд')); ?>
            </div>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]countryId"); ?>
                <?php echo CHtml::activeDropDownList($passport,"[$i]countryId",$countriesList); ?>
            </div>
            <div class="row">
                <?php echo CHtml::activeLabel($passport,"[$i]genderId"); ?>
                <?php echo CHtml::activeDropDownList($passport,"[$i]genderId",array(1=>'Мужской',2=>'Женский')); ?>
            </div>
            <?php echo CHtml::closeTag('fieldset');?>
        <?php endforeach; ?>

    <?php echo CHtml::submitButton('Сохранить'); ?>
    <?php echo CHtml::endForm(); ?>

    <?php //echo $form; ?>
</div>
