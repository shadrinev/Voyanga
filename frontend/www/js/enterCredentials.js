$(function(){
    var avia, hotels, tour;
    window.app = new Application();
    avia = new AviaModule();
    hotels = new HotelsModule();
    tour = new ToursModule();
    window.app.register('tours', tour, true);
    window.app.register('hotels', hotels);
    window.app.register('avia', avia);

    window.toursOverviewActive = true;
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
        $('.agreeConditions').on('click', function(){
            var checked = ($('#agreeCheck').is(':checked'));
            if (!checked)
                $('#submit-passport').removeClass('inactive');
            else
                $('#submit-passport').addClass('inactive');
        });
        $('#submit-passport').click(function () {
            var formData = $('#passport_form').serialize();
            var statuses = [],
                ids = [];
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
                    });
                    _.each(window.tripRaw.items, function(item, i){
                        $.ajax({
                            type: 'POST',
                            url: '/buy/makeBookingForItem?index='+i,
                            data: formData,
                            dataType: 'json'
                        })
                            .success(function(response){
                                statuses[i] = 1;
                                ids[i] = response.bookerId;
                                checkStatuses(statuses, ids);
                            })
                            .error(function(xhr, ajaxOptions, thrownError){
                                statuses[i] = xhr.responseText;
                                checkStatuses(statuses, ids);
                            });
                    });
                })
                .error(function(xhr, ajaxOptions, thrownError){
                    new ErrorPopup('passport500'); //ошибка, когда мы не смогли сохранить паспортные данные
                });
            });
        });
});

function checkStatuses(statuses, ids)
{
    var errors = '',
        errorText='',
        completed = true;
    _.each(statuses, function(el, i){
        if (el == 0)
            completed = false;
        if (_.isString(el))
            errors += 'Ошибка бронирования сегмента номер ' + (i+1) + ' = ' + el + '.<br>';
    });
    if (!completed)
        return;
    $.get('/buy/done', {ids: ids.join(',')})
        .done(function(){
            $.get('/buy/startPayment', function (data) {
                console.log('DATA AFTER START PAYMENT: ', data);
                if (data.error) {
                    new ErrorPopup('e500withText', 'Ошибка платёжной системы'); //ошибка бронирования
                } else {
                    //if everything is ok then go to payment
                    $('iframe').load(function(){
                        $('#loadPayFly').find('.armoring').hide();
                        $('#loadPayFly').find('.loadJet').hide();

                        $('.payCardPal').show();
                        $('.paybuyEnd').show();
                        Utils.scrollTo('.payCardPal',true);
                    });
                    Utils.submitPayment(data);
                }
            });
        })
        .error(function(){
            console.log('ERROR WHILE /buy/done')
        });
    if (errors.length>0)
    {
	    errorText = errors;
        new ErrorPopup('passportBookingError', [errorText]);
        return;
    }

}

initCredentialsPage = function() {
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
    window.app.bindItemsToBuy();
    ko.applyBindings(window.app);
    ko.processAllDeferredBindingUpdates();
    window.app.runWithModule(currentModule);
};
function InputCheckOn() {
    $('.tdDuration input[type="checkbox"]').each(function(index) {
        if ($(this).attr('checked') == 'checked') {
            $(this)
                .closest('tr')
                .prev()
                .find('.checkOn')
                .addClass('active')
                .find('input')
                .attr('disabled', 'disabled');
        }
        else {
            $(this)
                .closest('tr')
                .prev()
                .find('.checkOn')
                .removeClass('active')
                .find('input')
                .removeAttr('disabled');
        }
    });
}
function InputActiveFinishDate() {
    InputCheckOn();

    $('.tdDuration label.ui-hover').click(function() {
        InputCheckOn();
    });
}

$(window).load(InputActiveFinishDate);
