<?php
$this->breadcrumbs=array(
    'Orders'=>'/admin/orders/orderBooking', 'Order'
);
?>
<!-- HARDCORE -->
<style>
.table tbody tr:hover td,
.table tbody tr:hover th {
    background-color: white;
}

</style>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>ID заказа</th>
      <th>дата</th>
      <th>статус</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?= $data['id'] ?></td>
      <td><?= $data['timestamp'] ?></td>
      <td>UNIMPLEMENTED</td>
    </tr>
    <tr>
      <td colspan="3">
        <?php if(count($data['flightBookings'])): ?>
        <h3>Авиабилеты</h3>
        <table class="table table-bordered table-hover">
          <?php foreach($data['flightBookings'] as $booking) :?>
            <tr>
              <th>Наш номер брони</th>
              <th>Номер брони в Nemo</th>
              <th>PNR</th>
              <th>GDS</th>
              <th>Валидирующая А/К</th>
              <th>Откуда</th>
              <th>Куда</th>
              <th>Вылет</th>
              <th>Прибытие</th>
              <th>Стоимость</th>
              <th>Статус</th>
            </tr>
          <tr>
            <td>
              <?= $booking->id ?>
            </td>
            <td>
              <?= $booking->nemoBookId ?>
            </td>
            <td>
              <?= $booking->pnr ?>
            </td>
            <td>
              <?= "UNIMPLEMENTED" ?>
            </td>
            <td>
              <?= $booking->flightVoyage->valAirline->localRu; ?>
            </td>
            <td>
              <?= $booking->flightVoyage->departureCity->localRu; ?>
            </td>
            <td>
              <?= $booking->flightVoyage->arrivalCity->localRu; ?>
            </td>
            <td>
              <?= $booking->flightVoyage->departureDate; ?>
            </td>
            <td>
              UNIMPLEMENTED
            </td>
            <td>
              <?= $booking->price; ?> руб
            </td>
            <td>
              <?= $data['orderBooking']->stateAdapter($booking->status); ?>
            </td>
          </tr>
          <tr>
            <td colspan="11">
              <h4>Пассажиры</h4>
              <table class="table-bordered">
                <thead>
                  <tr>
                    <th>Номер билета</th>
                    <th>ФИО</th>
                    <th>Серия/Номер документа</th>
                  </tr>
                </thead>
                <?php foreach($booking->flightBookingPassports as $passport): ?>
                <tr>
                  <td>TODO</td>
                  <td><?= $passport->firstName; ?> <?= $passport->lastName; ?></td>
                  <td><?= $passport->series; ?>/<?= $passport->number; ?></td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
          </tr>
          <tr>
            <td colspan="11">
    </td>
    </tr>
          <?php endforeach; ?>
        </table>
        <?php endif; ?>

        <?php if(count($data['hotelBookings'])): ?>
        <h3>Отели</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Наш номер брони</th>
              <th>Поставщик</th>
              <th>Гостиница</th>
              <th>Город</th>
              <th>Заезд</th>
              <th>Выезд</th>
              <th>Стоимость</th>
              <th>Статус</th>
            </tr>
          </thead>
          <?php foreach($data['hotelBookings'] as $booking) :?>
          <tr>
            <td>
              <?= $booking->id; ?>
            </td>
            <td>
              UNIMPLEMENTED
            </td>
            <td>
              <?= $booking->hotel->hotelName; ?>
            </td>
            <td>
              <?= $booking->hotel->city->localRu; ?>
            </td>
            <td>
              <?= $booking->hotel->checkIn; ?>
            </td>
            <td>
              UNIMPLEMENTED
            </td>
            <td>
              <?= $booking->price; ?>
            </td>
            <td>
              <?= $data['orderBooking']->stateAdapter($booking->status); ?>
            </td>
          </tr>
          <tr>
            <!-- FIXME copypaste -->
            <td colspan="11">
              Гости отеля
              <table class="table-bordered">
                <thead>
                  <tr>
                    <th>Номер билета</th>
                    <th>ФИО</th>
                    <th>Серия/Номер документа</th>
                  </tr>
                </thead>
                <?php foreach($booking->hotelBookingPassports as $passport): ?>
                <tr>
                  <td>TODO</td>
                  <td><?= $passport->firstName; ?> <?= $passport->lastName; ?></td>
                  <td>UNIMPLEMENTED</td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
  </tbody>
</table>
