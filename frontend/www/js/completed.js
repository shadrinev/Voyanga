$(function(){
    window.toursOverviewActive = true;
});

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
                    $('#'+ind).text(newStatus);
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

