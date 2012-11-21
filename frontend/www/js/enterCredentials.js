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
    $(".chzn-select").chosen({
        no_results_text: "Нет соответствий"
    });
    $('input.next').keyup(function(){
        var $this= $(this),
            value = $this.val(),
            len = value.length,
            next = $this.next();
        if ($this.attr('maxlength')<=len)
        {
            next.select();
            next.focus();
        }
    });
    $(function () {
        $('#submit-passport').click(function () {
            var formData = $('#passport_form').serialize();
            var statuses = [];
            $('input').each(function() {
                $(this).attr({'disabled': 'disabled'});
            });
            $('#submit-passport').hide();
            $('#loadPayFly').find('.armoring').show();
            loadPayFly();
            $('#loadPayFly').find('.loadJet').show();
            $.ajax({
                type: 'POST',
                url: '/buy/makeBooking',
                data: formData,
                dataType: 'json'
            })
                .success(function(){
                    _.each(window.tripRaw.items, function(item, i){
                        statuses[i] = 0;
                        $.ajax({
                            type: 'POST',
                            url: '/buy/makeBookingForItem?index='+i,
                            data: formData,
                            dataType: 'json'
                        })
                            .success(function(){
                                statuses[i] = 1;
                                checkStatuses(statuses);
                            })
                            .error(function(xhr, ajaxOptions, thrownError){
                                statuses[i] = xhr.responseText;
                                checkStatuses(statuses);
                            });
                    })
                })
                .error(function(xhr, ajaxOptions, thrownError){
                    alert("ERROR: " + xhr.responseText);
                })
            });
        });
});

function checkStatuses(statuses)
{
    var errors = '';
    _.each(statuses, function(el, i){
        if (el == 0)
            return;
        if (_.isString(el))
            errors += 'Error while booking segment number ' + (i+1) + ' = ' + el + '. ';
    });
    $.get('/buy/done');
    if (errors.length>0)
    {
        alert(errors);
        return;
    }
    //if everything is ok then go to payment
    $('#loadPayFly').find('.armoring').hide();
    $('#loadPayFly').find('.loadJet').hide();
    $('.payCardPal').show();
    $('.paybuyEnd').show();
    $.get('/buy/startPayment', function (data) {
        if (data.error) {
            throw "Payment error";
        } else {
            Utils.submitPayment(data);
        }
    });
}

initCredentialsPage = function() {
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
    app.bindItemsToBuy()
    ko.applyBindings(app);
    ko.processAllDeferredBindingUpdates();
    app.runWithModule(currentModule);
};
