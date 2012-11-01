$(function(){
    $('.genderField').each(function(){
        var $this = $(this),
            value = $this.val(),
            element = $this.siblings('.gender-'+value);
        $this.siblings('.gender').each(function(){
            $(this).removeClass('active');
        });
        element.addClass('active');
    });
    $(document).on('click', '.gender', function(){
        var $this = $(this),
            value = $this.data('value'),
            field = $this.siblings('.genderField');
        $this.siblings('.gender').each(function(){
            $(this).removeClass('active');
        });
        $this.addClass('active');
        field.val(value);
    });
});

$(function() {
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
    var currentModule;
    switch (window.currentModule)
    {
        case 'Tours':
            currentModule = 'tours';
            break;
        case 'Avia':
            currentModule = 'avia';
            break;
        case 'Hotels':
            currentModule = 'hotels';
            break;
    }
    app.runWithModule(currentModule);
    ko.applyBindings(app);
    ko.processAllDeferredBindingUpdates();
});
