<div class="clear" id="paybuyContent"></div>
<div class="oneBlock">
    <!--=== ===-->
    <div class="paybuyContent">
        <h2>3. Данные покупателя</h2>
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
                    <?php echo CHtml::activeEmailField($model, "contactEmail", array('id' => 'contactEmail', 'placeholder' => 'example@mail.ru')); ?>
                </td>
                <td class="tdTelefon">
                    <div class="divInputTelefon <?php if ($hide) echo "inactive" ?>">
                        <?php echo CHtml::activeTextField($model, "contactPhone", array('id' => 'contactPhone', 'placeholder' => '+79101234567 ')); ?>
                    </div>
                </td>
                <td class="tdText">
                    На указанный адрес электронной почты будет выслано подтверждение бронирования
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!--=== ===-->
</div>
