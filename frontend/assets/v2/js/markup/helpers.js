/* RATING HOVER */
function ratingHoverActive(obj) {
    var that = $(obj);
    that.parent().addClass('hover');
}
function ratingHoverNoActive(obj) {
    var that = $(obj);
    that.parent().removeClass('hover');
}

function hideRecomendedBlockTicket() {
    if (!$(this).hasClass('show')) {
        $('.recomended-content').slideUp(function () {
            jsPaneScrollHeight();
        });
        $(this).addClass('show');
    }
    else {
        $(this).removeClass('show');
        $('.recomended-content').slideDown(function () {
            jsPaneScrollHeight();
        });

        $(window).load(inTheTwoLines);
        setTimeout(smallTicketHeight, 100);

    }
}
$(function () {
    bindActions = function () {
        $('.minimize-rcomended .btn-minimizeRecomended').click(function () {
        });
        $('.order-show').click(function () {
            $('.recomended-content').slideDown();
            $('.minimize-rcomended .btn-minimizeRecomended').animate({top: '-19px'}, 500);
            $(window).load(inTheTwoLines);

            otherTimeSlide();
            widthHowLong();
            setTimeout(smallTicketHeight, 100);
        });
        $('.descr').eq(1).hide();
        $('.place-buy .tmblr li a').click(function (e) {
            e.preventDefault();
            if (!$(this).hasClass('active')) {
                var var_nameBlock = $(this).attr('href');
                var_nameBlock = var_nameBlock.slice(1);
                $('.place-buy .tmblr li').removeClass('active');
                $(this).parent().addClass('active');
                $('.descr').hide();
                $('#' + var_nameBlock).show();
            }
        });
        $('.read-more').click(function () {
            if (!$(this).hasClass('active')) {
                $(this).prev().css('height', 'auto');
                $('#descr').find('.left').find(".descr-text .text").dotdotdot({watch: 'window'});
                $(this).addClass('active').text('Свернуть');
            }
            else {
                $(this).prev().css('height', '54px');
                $('#descr').find('.left').find(".descr-text .text").dotdotdot({watch: 'window'});
                $(this).removeClass('active').text('Подробнее');
            }
        });
        $('.stars-li input').each(function () {
            if ($(this).attr('checked') == 'checked') {
                $(this).next().addClass('active');
            }
        });
        $('.stars-li label').click(function () {
            if (!$(this).hasClass('active')) {
                $(this).addClass('active');
            }
            else {
                $(this).removeClass('active');
            }
        });
        var heCal = $('.calendarSlide').height();
        $('.calendarSlide').css('top', '-' + heCal + 'px');

        $('.input-path').click(function () {
            $('.calendarSlide').animate({'top': '0px'}, 400);
        });

        /* НА ГЛАВНОЙ СТРАНИЦЕ ОТВЕЧАЕТ ЗА НАЖАТИЕ НА СТАР ИЗ ГОРОДА! */
        $('.cityStart .to a').click(function () {
            var var_parent = $(this).parent().parent();
            var var_parentUp = var_parent.parent();
            var_parentUp.find('.from').addClass('overflow').animate({'width': '125px'}, 300);
            var_parent.find('.startInputTo').show();
            var_parent.animate({'width': '261px'}, 300, function () {
                var_parent.find('.startInputTo').find('input').focus();
            });
        });
        $('.board-content .from input').click(function () {
            if ($(this).parent().hasClass('overflow')) {
                $(this).parent().animate({'width': '271px'}, 300, function () {
                    $(this).removeClass('overflow');
                });
                $('.cityStart').animate({'width': '115px'}, 300);
                $('.cityStart').find('.startInputTo').animate({'opacity': '1'}, 300, function () {
                    $(this).hide();
                });
            }
        });
    }
});

function deletePopUp(obj) {
    var _this = $(obj);
    var pos = _this.offset();
    $('body').prepend('<div class="deletePopUp">Удалить?</div>');
    $('.deletePopUp').css('top', (pos.top - 28) + 'px').css('left', (pos.left - 6) + 'px');
}
function deletePopUpHide() {
    $('.deletePopUp').remove();
}

function telefonLoad() {
    if ($('#contactPhone').length > 0 && $('#contactPhone').is(':visible')) {
        $('#contactPhone').mask('+7 (999) 999-99-99');
    }
    else {
        return
    }

    if ($('.allTicketsDIV .ticketBox').length > 0 && $('.allTicketsDIV .ticketBox').is(':visible')) {
        var _ticketBoxLen = $('.allTicketsDIV .ticketBox').length;
        if (_ticketBoxLen == 1) {
            $('.allTicketsDIV .ticketBox').addClass('first-child').addClass('last-child');
        }
        else {
            for (i = 0; i < _ticketBoxLen; i++) {
                if (i == 0) {
                    $('.allTicketsDIV .ticketBox').eq(i).addClass('first-child');
                }
                else if (i == (_ticketBoxLen - 1)) {
                    $('.allTicketsDIV .ticketBox').eq(i).addClass('last-child');
                }
                else {
                    return true;
                }
            }
        }
    }
    else {
        return;
    }

    $('.infoPassengers:eq(0) tbody .tdName:eq(0) input').focus(function () {
        Utils.scrollTo('#tableStartRun');
    });
}
$(window).load(telefonLoad);


function onFocusInput() {
    if ($('.male input').attr('checked') == 'checked') {
        $('.male input').closest('label').addClass('active');
    }
    if ($('.female input').attr('checked') == 'checked') {
        $('.female input').closest('label').addClass('active');
    }
    $('.male input').focus(function () {
        $(this).parent().addClass('focus');
        var _that = this;
        $(window).unbind('keypress');
        $(window).bind('keypress', function(e) {
            if (e.which == 9 && $(_that).attr('checked') == 'checked') {
                $(_that).parent().parent().next().find('input.dd').focus();
            }
            else if (e.which == 9 && $(_that).attr('checked') != 'checked') {
                $('.female input').focus();
            }
            else if (e.which == 32) {
                $('.male').removeClass('active error');
                $('.female').removeClass('active error');
                $(_that).parent().addClass('active');
                $(_that).parent().parent().next().find('input.dd').focus();
                $(_that).attr('checked','checked');
            }
        });
    });
    $('.male input').blur(function () {
        $(this).parent().removeClass('focus');
        $(this).removeAttr('disabled');
        $('.female input').removeAttr('disabled');
    });
    $('.male input').change(function () {
        if ($(this).attr('checked') == 'checked') {
            $(this).parent().addClass('active');
            $('.female').removeClass('active error');
        }
        else {
            $(this).parent().removeClass('active');
        }
    });
    $('.female input').focus(function () {
        $(this).parent().addClass('focus');
        var _that = this;
        $(window).unbind('keypress');
        $(window).bind('keypress', function(e) {
            if (e.which == 9 && $(_that).attr('checked') == 'checked') {
                $(_that).parent().parent().next().find('input.dd').focus();
            }
            else if (e.which == 9 && $(_that).attr('checked') != 'checked') {
                $(_that).parent().parent().next().find('input.dd').focus();
            }
            else if (e.which == 32) {
                $('.male').removeClass('active error');
                $('.female').removeClass('active error');
                $(_that).parent().addClass('active');
                $(_that).parent().parent().next().find('input.dd').focus();
                $(_that).attr('checked','checked');
            }
        });
    });
    $('.female input').blur(function () {
        $(this).parent().removeClass('focus');
        $(this).removeAttr('disabled');
        $('.male input').removeAttr('disabled');
    });
    $('.female input').change(function () {
        if ($(this).attr('checked') == 'checked') {
            $(this).parent().addClass('active');
            $('.male').removeClass('active error');
        }
        else {
            $(this).parent().removeClass('active');
        }
    });
}
$(window).load(onFocusInput);

function showUserMenu() {

    $('.popupDown').slideDown(200, function() {
        $('.login-window').attr('onclick',' ');
    });
    // Проверка на закрытие вне области
    var mouseHover = true;

    $('.popupDown').hover(function () {
            mouseHover = false;
        },
        function () {
            mouseHover = true;
        }
    );
    $('body').mouseup(function () {
        if (mouseHover) {
            console.log(mouseHover);
            $('.popupDown').slideUp(100, function() {
                $('.login-window').attr('onclick','showUserMenu()');
            });
        }
        else {
            return false;
        }
    });
}

function hideFromCityInput(event) {
    var _targetYes = event.target;
    if (!$(_targetYes).parents('.cityStart').length > 0) {
    var elB, elem, elemB, startInput, toInput;
    if (!$('.startInputTo').is(':visible')) {
        return;
    }
    elemB = $('.cityStart').find('.second-path');
    elB = elemB.closest('.cityStart');
    elB.closest('.tdCityStart').animate({
        width: '-=130',
        300: 300
    });
    elB.closest('.tdCityStart').find('.bgInput').animate({
        width: '-=150',
        300: 300
    });
    elB.closest('.tdCityStart').next().find('.data').animate({
        width: '+=130',
        300: 300
    });
    elem = $('.startInputTo .second-path');
    startInput = $('div.startInputTo');
    toInput = $('div.overflow');
    if (startInput.is(':visible')) {
        toInput.animate({
            width: "271px"
        }, 300, function () {
            return toInput.removeClass("overflow");
        });
        $(".cityStart").animate({
            width: "115px"
        }, 300);
        return startInput.animate({
            opacity: "1"
        }, 300, function () {
            return startInput.hide();
        });
    }
    }
    else {
        return true;
    }
}

$(function () {
    $('html').on('click', function (e) {
        hideFromCityInput(e);
    });
});

function nextSlideDownRules(_this) {
    if ($(_this).hasClass('active')) {
        $(_this).next().slideUp();
    }
    else {
        $(_this).next().slideDown();
        $(_this).addClass('active')
    }

}

function getLink() {
    $('body').click(function(e) {
        if ($(e.target).parents('#followLink').length > 0) {
            $('#followLink').find('.text').hide();
            $('#followLink').find('.getLink').show();
        }
        else {
            $('#followLink').find('.text').show();
            $('#followLink').find('.getLink').hide();
        }
    });
}
$(window).load(getLink);