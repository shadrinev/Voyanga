<td colspan="<?= $span[0]?>">
  <h4>Платежная информация</h4>
  <?php if($booking->bill): ?>
  <table class="table-bordered">
    <tr><th>Счет #</th><td><?= $booking->bill->id?></td></tr>
    <tr><th>Канал</th><td><?= $booking->bill->channelVerbose?></td></tr>
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
      <?php if($booking->status == 'swFlightBooker/ticketingError'): ?>
      <b>Ввод результатов ручной выписки</b>
      <form method="POST" action="<?= $this->createUrl('injectTicketNumbers', array('bookingId'=>$booking->id)); ?>">
        <ol>
        <?php foreach($booking->flightBookingPassports as $passport): ?>
              <li>Номер билета  для <?= $passport->firstName; ?> <?= $passport->lastName; ?>
                <br />
                <input type="text" name="tickets[<?= $passport->id?>]">
              </li>
        <?php endforeach; ?>
        </ol>
        <ul>
        <?php foreach($booking->flightVoyage->flights as $fvkey=>$flight): ?>
          <?php foreach($flight->flightParts as $fkey=>$part): ?>
          <li> Терминал для <?=$part->departureAirport->code;?>
          <input name="terminal[<?=$fvkey?>][<?=$fkey?>]" type="text" value="<?= $part->departureTerminalCode ?>" style="width:40px !important;">
          <?php endforeach; ?>
        <?php endforeach; ?>
        </ul>
        <input type='submit' value="Выписать"> *Письмо не будет отправлено автоматически
      </form>
      <?php endif; ?> 

    </td></tr>
  </table>
</td>