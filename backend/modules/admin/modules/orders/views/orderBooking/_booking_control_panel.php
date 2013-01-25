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
  <?php if($booking instanceof FlightBooker): ?>
  <h4>Тарифы/Сборы</h4>
  <table class="table-bordered">
    <tr><th>BaseFare</th><td><?= $booking->flightVoyage->baseFare?></td></tr>

    <tr><th>Taxes</th><td><?= $booking->flightVoyage->taxes?></td></tr>

    <tr><th>Charges</th><td><?= $booking->flightVoyage->charges?></td></tr>
    <tr><th>Сумма</th><td><?= $booking->flightVoyage->price?> руб.</td></tr>
  </table>
  <?php endif; ?>
</td>
<td colspan="<?= $span[2]?>">
  <h4>Пульт управления</h4>
  <table class="table-bordered">
    <tr><th>Статус</th><td><?= $booking->status ?></td></tr>
    <tr><th>Действия</th><td>
      <ul>
        <?php foreach ($this->adminActions($booking) as $title => $url):?>
          <li><a href="<?= $url?>"><?=$title?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php if($booking->status == 'swFlightBooker/ticketingRepeat'): ?>
      <b>Ввод результатов ручной выписки</b>
      <ol>
        <form method="POST" action="<?= $this->createUrl('injectTicketNumbers', array('bookingId'=>$booking->id)); ?>">
        <?php foreach($booking->flightBookingPassports as $passport): ?>
              <li>Номер билета <input type="text" name="tickets[<?= $passport->id?>]"> для <?= $passport->firstName; ?> <?= $passport->lastName; ?></li>
        <?php endforeach; ?>
        <input type='submit' value="Выписать"> *Письмо не будет отправлено автоматически
        </form>
      </ol>
      <?php endif; ?> 

    </td></tr>
  </table>
</td>