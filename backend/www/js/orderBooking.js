/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 20.07.12
 * Time: 13:33
 * To change this template use File | Settings | File Templates.
 */
document.showOrderInfo = function (id){
    id = id.substr(1);
    $('#popupInfo .modal-body').html("<p>Идет запрос данных...</p>");
    $('#popupInfo').modal('show');
    $.getJSON("/admin/orders/orderBooking/getInfo/id/"+id)
        .done(function(data){
            //console.log(data);
            $('#popupInfo .modal-body').html(orderBookingTemplate(data));
        })
        .fail(function(data){
            $('#popupInfo .modal-body').html("Ошибка сервера");
            //btn.button("reset");
            //btn.html("Произошёл сбой");
            //e.preventDefault();
        });
};

$(function(){
    if(window.location.hash){
	var id = window.location.hash;
	document.showOrderInfo(id);
    }
});
