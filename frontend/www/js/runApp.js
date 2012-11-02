$(function() {
    var app, avia, hotels, tour;
    console.time("App dispatching");
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
    app.run();
    console.timeEnd("App dispatching");
    console.time("Rendering");
    ko.applyBindings(app);
    ko.processAllDeferredBindingUpdates();
    return console.timeEnd("Rendering");
});