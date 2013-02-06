<td colspan="4">
</td>
<td>
<ul>
<?php foreach ($this->orderActions($orderBooking) as $title => $url):?>
  <li><a href="<?= $url?>"><?=$title?></a></li>
<?php endforeach; ?>
</ul>
</td>