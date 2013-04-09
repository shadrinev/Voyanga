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
    $('#passport_form').find('input[id!="contactPhone"]').bind('click', function() {
        $(this).select();
    });

    $('.male input').each(function(index) {
        if ($('.male').eq(index).find('input').attr('checked') == 'checked') {
            $('.male').eq(index).find('input').closest('label').addClass('active');
            $('.male').eq(index).find('input').attr('checked','checked');
        }
    });
    $('.female input').each(function(index) {
        if ($('.female').eq(index).find('input').attr('checked') == 'checked') {
            $('.female').eq(index).find('input').closest('label').addClass('active');
            $('.female').eq(index).find('input').attr('checked','checked');
        }
    });
    $('.male input').focusin(function(e) {
        $(this).parent().addClass('focus');
        if ($.browser.opera) {
            return true;
        }
        else {
            $(this).removeAttr('checked');
        }
    });
    $('.male input').blur(function () {
        $(this).parent().removeClass('focus');

    });
    $('.male input').change(function () {
        $(this).parent().removeClass('active');
        $(this).parent().next().removeClass('active');
        $(this).parent().addClass('active');
        $(this).attr('checked','checked');
        if ($(this).attr('ckecked') == 'checked') {

        }
        else {

        }
        $(this).parent().parent().next().find('input.dd').focus();
    });

    $('.female input').focusin(function() {
        $(this).parent().addClass('focus');
        if ($.browser.opera) {
            return true;
        }
        else {
            $(this).removeAttr('checked');
        }

    });
    $('.female input').blur(function () {
        $(this).parent().removeClass('focus');

    });
    $('.female input').change(function () {
        $(this).parent().removeClass('active');
        $(this).parent().prev().removeClass('active');
        $(this).parent().addClass('active');
        $(this).attr('checked','checked');
        if ($(this).attr('ckecked') == 'checked') {

        }
        else {

        }
        $(this).parent().parent().next().find('input.dd').focus();
    });
//    $('.female input').click(function() {
//        $(this).parent().prev().find('input').removeAttr('checked');
//        $(this).attr('checked','checked');
//    });
    $(window).unbind('keydown');
    $(window).bind('keydown', function(e) {
        if (e.which == 0 || e.which == 9 || e.which == 39 || e.which == 37) {
            if ($(e.target).parents('.male').length > 0) {
                e.preventDefault();
                var _this = $(e.target);
                if ($(_this).is(':checked')) {
                    $(_this).parent().parent().next().find('input.dd').focus();
                }
                else {
                    $(_this).parent().next().find('input').focus();
                    $(_this).parent().removeClass('active');
                }
            }
            else if ($(e.target).parents('.female').length > 0) {
                e.preventDefault();
                var _this = $(e.target);
                if ($(_this).is(':checked')) {
                    $(_this).parent().parent().next().find('input.dd').focus();
                }
                else {
                    $(_this).parent().prev().find('input').focus();
                    $(_this).parent().removeClass('active');
                }
            }
        }
        else if (e.which == 32) {
            var _this = $(e.target);
            $(_this).parent().addClass('active');
            //$(_this).attr('checked','checked');
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
function divInputBirthday() {
    if($('.divInputBirthday').length > 0 && $('.divInputBirthday').is(':visible')) {
//        var _this = $('.divInputBirthday').find('input');
//        _this.focus(function() {
//            $(this).attr('rel', $(this).val());
//            $(this).val('');
//            $(this).blur(function() {
//                if($(this).val() < 1) {
//                    $(this).val($(this).attr('rel'));
//                }
//                else {
//                    return;
//                }
//            });
//        });
    }
}
$(window).load(divInputBirthday);
function getLink() {
    $('body').click(function(e) {
        if ($(e.target).parents('#followLink').length > 0 || $(e.target).attr('id') == 'followLink') {
            $('#followLink').find('.text').hide();
            $('#followLink').find('.getLink').show();
            $('#followLink').find('.getLink input').focus().select();
        }
        else {
            $('#followLink').find('.text').show();
            $('#followLink').find('.getLink').hide();
        }
    });
}
$(window).load(getLink);


function DayMonthYear() {
    if($('.divInputBirthday').length > 0 && $('.divInputBirthday').is(':visible')) {

        var _birthday = $('.tdBirthday').find('.divInputBirthday').find('input');

        _birthday.keypress(function(e) {
            if (e.which > 47 && e.which < 58) {

                _birthday.keyup(function(e) {

                    if ($(this).hasClass('dd')) {
                        console.log('DD = ' + e.which +' '+ $(this).hasClass('dd') + ' '+ $(this).val()+ ' '+ $(this).val().length);
                        if ($(this).val() > 31) {
                            $(this).addClass('error');
                        }
                        else {
                            $(this).removeClass('error');
                        }
                        if (e.which == 9) {
                            if ($(this).val().length < 2) {
                                if ($(this).val() == 0) {
                                    $(this).addClass('error');
                                }
                                else {
                                    $(this).val('0'+$(this).val());
                                    $(this).removeClass('error');
                                    $(this).next().focus();
                                }
                            }
                            else {
                                return true;
                            }
                        }

                    }
                    else if ($(this).hasClass('mm')) {
                        console.log('MM = ' + e.which +' '+ $(this).hasClass('dd') + ' '+ $(this).val()+ ' '+ $(this).val().length);
                        if ($(this).val() > 12) {
                            $(this).addClass('error');
                        }
                        else {
                            $(this).removeClass('error');
                        }

                        if (e.which == 9) {
                            if ($(this).val().length < 2) {
                                if ($(this).val() == 0) {
                                    $(this).addClass('error');
                                }
                                else {
                                    $(this).val('0'+$(this).val());
                                    $(this).removeClass('error');
                                    $(this).next().focus();
                                }
                            }
                            else {
                                return true;
                            }
                        }

                    }
                    else if ($(this).hasClass('yy')) {
                        console.log('YY = ' + e.which +' '+ $(this).hasClass('dd') + ' '+ $(this).val()+ ' '+ $(this).val().length);
                        if (e.which == 9) {
                            if ($(this).val().length > 1 && $(this).val().length < 3) {
                                if ($(this).val() < 14) {
                                    $(this).val('20'+$(this).val());
                                    $(this).removeClass('error');
                                    $(this).parent().parent().next().next().find('input').focus();
                                }
                                else {
                                    $(this).val('19'+$(this).val());
                                    $(this).removeClass('error');
                                    $(this).parent().parent().next().next().find('input').focus();
                                }
                            }
                            else if ($(this).val().length < 2 && $(this).val().length > 0) {
                                $(this).val('200'+$(this).val());
                                $(this).removeClass('error');
                                $(this).parent().parent().next().next().find('input').focus();
                            }
                            else if ($(this).val().length > 3) {
                                $(this).parent().parent().next().next().find('input').focus();
                            }
                            else {
                                $(this).addClass('error');
                            }
                        }
                        else {
                            return true;
                        }

                    }
                    else {
                        console.log(e.which +' '+ $(this).hasClass('dd') + ' '+ $(this).val()+ ' '+ $(this).val().length);
                    }

                });

            }
            else if (e.which == 8) {
                $(this).removeClass('error');
                return true;
            }
            else if (e.which == 9) {
                $(this).removeClass('error');
                return true;
            }
            else {
                return false;
            }
        });

        _birthday.focusout(function() {
            if ($(this).hasClass('dd')) {
                if ($(this).val() > 31) {
                    $(this).addClass('error');
                }
                else {
                    if ($(this).val().length < 2) {
                        if ($(this).val() == 0) {
                            $(this).addClass('error');
                        }
                        else {
                            $(this).val('0'+$(this).val());
                            $(this).removeClass('error');
                            $(this).next().focus();
                        }
                    }
                    else {
                        return true;
                    }
                }

            }
            else if ($(this).hasClass('mm')) {

                if ($(this).val() > 12) {
                    $(this).addClass('error');
                }
                else {
                    if ($(this).val().length < 2) {
                        if ($(this).val() == 0) {
                            $(this).addClass('error');
                        }
                        else {
                            $(this).val('0'+$(this).val());
                            $(this).removeClass('error');
                            $(this).next().focus();
                        }
                    }
                    else {
                        return true;
                    }
                }

            }
            else if ($(this).hasClass('yy')) {
                if ($(this).val().length > 1 && $(this).val().length < 3) {
                    if ($(this).val() < 14) {
                        $(this).val('20'+$(this).val());
                        $(this).removeClass('error');
                        $(this).parent().parent().next().next().find('input').focus();
                    }
                    else {
                        $(this).val('19'+$(this).val());
                        $(this).removeClass('error');
                        $(this).parent().parent().next().next().find('input').focus();
                    }
                }
                else if ($(this).val().length < 2 && $(this).val().length > 0) {
                    $(this).val('200'+$(this).val());
                    $(this).removeClass('error');
                    $(this).parent().parent().next().next().find('input').focus();
                }
                else if ($(this).val().length > 3) {
                    $(this).parent().parent().next().next().find('input').focus();
                }
                else {
                    $(this).addClass('error');
                }

            }
        });

    }
}