<form action="<?= $paymentUrl ?>" method="POST" target="payframe">
<?php foreach($params as $key=>$value): ?>
     <input type="hidden" name="<?= $key;?>" value="<?= $value; ?>">
<?php endforeach; ?>
<input type="submit" value="pay" />
</form>

<iframe name="payframe"  width="99%" height="600"></iframe>