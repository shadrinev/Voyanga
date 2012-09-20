<h4>Гостиница</h4>
<table class="table table-bordered" width="100%">
    <thead>
    <tr>
        <th>Въезд</th>
        <th>Выезд</th>
        <th>Где</th>
        <th>Отель</th>
        <th>Цена</th>
        <th width="18%"></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{checkIn}}</td>
        <td>{{checkOut}}</td>
        <td>{{city}}</td>
        <td>{{hotelName}}({{category}})</td>
        <td><b>{{rubPrice}} руб.</b></td>
        <td><a class="btn btn-warning detail-view" data-key='{{key}}'>Подробнее</a>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <table class="table table-bordered detail-{{../key}}" style='display: none; background-color: #f0f0f0'>
                <thead>
                    <tr>
                        <th>Размер</th>
                        <th>Тип</th>
                        <th>Размещение</th>
                        <th>Питание</th>
                        <th>Завтрак</th>
                    </tr>
                </thead>
                <tbody>
                {{#each rooms}}
                    <tr>
                        <td>{{size}}</td>
                        <td>{{type}}</td>
                        <td>{{view}}</td>
                        <td>{{meal}}</td>
                        <td>{{mealBreakfast}}</td>
                    </tr>
                </tbody>
                {{/each}}
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="background-color: #b2e5ff; height: 5px;">&nbsp;</td>
    </tr>
    </tbody>
</table>