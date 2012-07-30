<form action="https://test.wpay.uniteller.ru/pay/" method="POST">
<?php foreach($fields as $name=>$value): ?>
     <input type="hidden" name="<?= $name ?>" value="<?= $value ?>">
<?php endforeach; ?>
<input type="submit" value="GOGOGO">
</form>