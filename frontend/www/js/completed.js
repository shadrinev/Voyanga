$(function(){
    window.toursOverviewActive = true;
});

function mapping(status)
{
    var dictionary = {
        "booking" : "Бронирование",
        "reBooking" : "Бронирование",
        "bookingError" : "Ошибка бронирования",
        "waitingForPayment" : "Ожидание оплаты",
        "paymentInProgress" : "Ожидание оплаты",
        "paymentError" : "Ошибка оплаты",
        "paymentCanceledError" : "Ошибка оплаты",
        "paid" : "Ожидание оплаты",
        "refundedError" : "Ошибка оплаты",
        "bookingTimeLimitError" : "Ошибка бронирования",
        "ticketing" : "Выписка билета",
        "ticketReady" : "Выписка билета",
        "ticketingRepeat" : "Выписка билета",
        "manualProcessing" : "Выписка билета",
        "manualTicketing" : "Выписка билета",
        "ticketingError" : "Ошибка выписки",
        "manualError" : "Ошибка выписки",
        "moneyReturn" : "Возврат денег",
        "manualSuccess" : "скачать PDF",
        "confirmMoney" : "Ожидание оплаты",
        "done" : "скачать PDF",
	    "canceled": "Отменен",
	    "canceledByUser": "Отменен",
        "error" : "Ошибка"
    }
    if (dictionary[status] != "undefined")
        return dictionary[status];
    return status;
}

function isSuccess(status)
{
    //! ЕМНИП ручной успех все равно доталкивается до дана.
    return ((status == 'manualSuccess') || (status =='done'));
}

initCompletedPage = function() {
    var app, avia, hotels, tour;
    window.voyanga_debug = function() {
        var args;
        args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
        return console.log.apply(console, args);
    };
    app = new Application();
    avia = new AviaModule();
    hotels = new HotelsModule();
    tour = new ToursModule();
    window.app = app;
    app.register('tours', tour, true);
    app.register('hotels', hotels);
    app.register('avia', avia);
    var currentModule = 'tours';
    app.bindItemsToBuy();
    ko.applyBindings(app);
    ko.processAllDeferredBindingUpdates();
    app.runWithModule(currentModule);
    $('input').each(function(){
        $(this).attr('disabled', 'disabled');
    });

    var initDate = new Date(); // Or get the user login date from an HTML element (i.e. hidden input)
    var interval;

    function keepAlive() {
        $('#updateStatus').fadeIn();
        $.ajax({
            url: '/buy/status/id/'+window.orderId,
            dataType: 'json'
        })
            .done(function(response){
                _.each(window.tripRaw.items, function(el, i){
                    var ind = el.key,
                        newStatus = response[ind];
                    statusReadable = mapping(newStatus);
                    if (isSuccess(newStatus))
                    {
                        var link = "<a href='/buy/pdf/id/" + el.key + "'>" + statusReadable + "</a>";
                        $('#'+ind).html(link);
                        $('#'+ind).parent().removeClass('wait').addClass('download');
                    }
                    else
                    {
                        $('#'+ind).text(statusReadable);
                        $('#'+ind).parent().removeClass('download').addClass('wait');
                    }
                });
                $('#updateStatus').fadeOut();
            })
            .error(function(){
                $('#updateStatus').fadeOut();
            });
    }

    window.onload = function () {
        keepAlive();

        interval = window.setInterval(function () {
            var now = new Date();
            if (now.getTime() - initDate.getTime() < 30 * 60 * 1000 && now.getDate() == initDate.getDate()) {
                keepAlive();
            }
            else {
                // Stop the interval
                window.clearInterval(interval);
            }
        }, 15 * 1000);
    }
};

