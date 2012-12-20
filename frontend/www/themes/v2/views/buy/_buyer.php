<div class="oneBlock">
    <!--=== ===-->
    <div class="paybuyContent">
        <h2>Покупатель</h2>
        <table class="dopInfoPassengers">
            <thead>
            <tr>
                <td>Адрес электронной почты</td>
                <td>Номер телефона</td>
                <td></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="tdEmail">
                    <?php echo CHtml::activeTextField($model, "contactEmail", array('id' => 'contactEmail')); ?>
                </td>
                <td class="tdTelefon">
                    <div class="divInputTelefon <?php if ($hide) echo "inactive" ?>">
                        <input disabled="disabled" value="+7" class="plusSeven" name="plusSeven" type="text">
                        <?php echo CHtml::activeTextField($model, "contactPhone", array('id' => 'contactPhone')); ?>
                    </div>
                </td>
                <td class="tdText">
                    Чтобы мы знали куда прислать электронный билет и куда звонить в случае каких-либо изменений
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!--=== ===-->
</div>