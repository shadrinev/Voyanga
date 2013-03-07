$(function() {
    var app, avia, hotels, tour;
    app = new Application();
    avia = new AviaModule();
    hotels = new HotelsModule();
    tour = new ToursModule();
    window.app = app;
    app.register('tours', tour, true);
    app.register('hotels', hotels);
    app.register('avia', avia);
    app.run();
    ko.applyBindings(app);
    ko.processAllDeferredBindingUpdates();
});