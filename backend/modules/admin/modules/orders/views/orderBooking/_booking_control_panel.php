<td colspan="<?= $span[0]?>">
  <h4>Платежная информация</h4>
  <?php if($booking->bill): ?>
  <table class="table-bordered">
    <tr><th>Счет #</th><td><?= $booking->bill->id?></td></tr>
    <tr><th>Статус счета</th><td><?= $booking->bill->status?></td></tr>
    <tr><th>Транзакция</th><td><?= $booking->bill->transactionId?></td></tr>
    <tr><th>Сумма</th><td><?= $booking->bill->amount?> руб.</td></tr>
  </table>
  <?php else: ?>
  Нет связанных счетов для элемента заказа.
  <?php endif; ?>
</td>
<td colspan="<?= $span[1]?>">
  <h4>Пульт управления</h4>
  <table class="table-bordered">
    <tr><th>Статус</th><td><?= $booking->status ?></td></tr>
    <tr><th>Действия</th><td>
      <ul>
        <?php foreach ($this->adminActions($booking) as $title => $url):?>
          <li><a href="<?= $url?>"><?=$title?></a></li>
        <?php endforeach; ?>
      </ul>
    </td></tr>
  </table>
</td>