$(function() {
    var app, avia, hotels, tour;
    console.time("App dispatching");
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