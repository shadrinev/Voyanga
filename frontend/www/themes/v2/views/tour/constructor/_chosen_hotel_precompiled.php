<table class="table" width="100%">
    <tbody>
    <tr>
        <td><?php echo $hotel->hotelName;?></td>
        <td><?php echo $hotel->rubPrice;?> руб.</td>
    </tr>
    <tr>
        <td colspan="3" style="padding-left: 25px;">
            <table class="table" width="100%">
                <tbody>
                <?php foreach($hotel->rooms as $hotelRoom): ?>
                <tr>
                    <td><?php echo $hotelRoom->sizeName;?></td>
                    <td><?php echo $hotelRoom->typeName;?></td>
                    <td><?php echo $hotelRoom->viewName;?></td>
                    <td><?php echo $hotelRoom->mealName;?></td>
                    <td><?php echo $hotelRoom->mealBreakfastName;?></td>
                </tbody>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>
    </tbody>
</table>
