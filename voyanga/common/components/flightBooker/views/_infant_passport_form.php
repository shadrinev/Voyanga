<div class="well">
    <?php echo $form->textFieldRow($passport, "[$i]firstName");?>
    <?php echo $form->textFieldRow($passport, "[$i]lastName");?>
    <?php echo $form->textFieldRow($passport,"[$i]birthday");?>
    <?php echo $form->radioButtonListInlineRow($passport,"[$i]genderId", Passport::getPossibleGenders());?>
    <?php echo $form->dropDownListRow($passport,"[$i]countryId", Country::getPossibleCountries());?>
    <?php echo $form->dropDownListRow($passport,"[$i]documentTypeId", Passport::getPossibleTypes());?>
    <?php echo $form->textFieldRow($passport,"[$i]series");?>
    <?php echo $form->textFieldRow($passport,"[$i]number");?>
</div>