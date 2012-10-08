function sliderPhoto(that) {
    var var_this = $(that).eq(0);
    var var_len = var_this.find('ul > li').length;
    var_this.find('ul li').eq(0).addClass('active');
    var var_widthAll = var_this.width();
    var var_widthUL = 0;
    for (a = 0; a < var_len; a++) {
	var_widthUL += var_this.find('ul li').eq(a).find('img').width() + 1;
        console.log("UL LENGTH", var_widthUL, var_this.find('ul li').eq(a).find('img').width() + 1);
    }
    var_this.find('ul').css('width', var_widthUL+'px');
    $('.left-navi').hide();

    var var_widthULminus = var_widthUL - var_widthAll;
    var one_short = false;

    $('.right-navi').unbind('click');
    $('.right-navi').click(function() {
	if (! one_short) {
	    one_short = true;
	    var_this.find('.left-navi').show();
	    var var_widthOne = (var_this.find('ul .active').width() + 1);
	    var var_margin = var_this.find('ul').css('margin-left');
	    var_margin = Math.abs(parseInt(var_margin.slice(0,-2)));
	    var var_index = var_this.find('ul .active').index();
	    var all = var_margin+var_widthOne;
	    if (all <= var_widthULminus) {
		var_this.find('ul').animate({'margin-left' : '-='+var_widthOne+'px'}, 200, function() {
		    one_short = false;
		});
		var_this.find('ul .active').removeClass('active').next().addClass('active');
	    }
	    else {
		$(this).hide();
		var_widthOne = (var_this.find('ul li').eq(var_len-1).width() + 1);
		console.log(var_widthOne +' '+all+' '+var_widthUL+' '+var_widthULminus+' '+var_widthAll);
		var var_widthEnd = (var_widthUL - var_widthAll) - 3;
		var_this.find('ul').animate({'margin-left' : '-'+var_widthEnd+'px'}, 200, function() {
		    one_short = false;
		});
		var_this.find('ul li').removeClass('active');
		var_this.find('ul li').eq(var_len-1).addClass('active');
	    }
	}
    });
    $('.left-navi').unbind('click');
    $('.left-navi').click(function() {
	if (! one_short) {
	    one_short = true;
	    var_this.find('.right-navi').show();
	    var var_widthOne = (var_this.find('ul .active').width() + 1);
	    var var_margin = var_this.find('ul').css('margin-left');
	    var_margin = Math.abs(parseInt(var_margin.slice(0,-2)));
	    if ((var_margin-var_widthOne) > 0) {
		var_this.find('ul').animate({'margin-left' : '+='+var_widthOne+'px'}, 200, function() {
		    one_short = false;
		});
		var_this.find('ul .active').removeClass('active').prev().addClass('active');
	    }
	    else {
		var_this.find('ul').animate({'margin-left' : '0px'}, 200, function() {
		    one_short = false;
		});
		var_this.find('ul li').removeClass('active');
		var_this.find('ul li').eq(0).addClass('active');
		$(this).hide();
	    }
	}
    });
}

function checkUlList() {
    $('.details').each(function() {
	var var_this = $(this).find('ul li');
	var var_length = var_this.length;
	for (i = 0; i < var_length; i++) {
	    if (i == 0 || i == 1) {
		var_this.eq(i).addClass('not-show');
	    }
	    else {
		var_this.eq(i).hide();
	    }
	}
    });
    $('.tab-ul a').click(function() {
	var var_thisLink = $(this);
	var var_this = $(this).parent().parent();
	if (! $(this).hasClass('active')) {
	    var_thisLink.text('Свернуть все рузультаты');
	    var_thisLink.addClass('active');
	    var_this.find('ul li[class != "not-show"]').slideDown();
	}
	else {
	    var_this.find('ul li[class != "not-show"]').slideUp(300, function() {
		var_thisLink.removeClass('active');
		var_thisLink.text('Посмотреть все результаты');
	    });
	}
    });
    
}

function hideRecomendedBlockTicket() {
	if (!$(this).hasClass('show')) {
		$('.recomended-content').slideUp(function() {
			ifHeightMinAllBody();
		});
		$(this).addClass('show');	
	}
	else {
		$(this).removeClass('show');
		$('.recomended-content').slideDown(function() {
			ifHeightMinAllBody();
		});
		
		$(window).load(inTheTwoLines);
		setTimeout(smallTicketHeight, 100);
		
		/*
		otherTimeSlide();
		widthHowLong();
		
*/
	}
}
//$(window).load(checkUlList);
$(function() {

bindActions = function() {

	$('.minimize-rcomended .btn-minimizeRecomended').click(function() {
	});

    $('.order-show').click(function() {
		$('.recomended-content').slideDown();
		$('.minimize-rcomended .btn-minimizeRecomended').animate({top : '-19px'}, 500);
		$(window).load(inTheTwoLines);

		otherTimeSlide();
		widthHowLong();
		setTimeout(smallTicketHeight, 100);
	});

	$('.descr').eq(1).hide();
	$('.place-buy .tmblr li a').click(function(e) {
		e.preventDefault();
		if (! $(this).hasClass('active')) {
			var var_nameBlock = $(this).attr('href');
			var_nameBlock = var_nameBlock.slice(1);
			$('.place-buy .tmblr li').removeClass('active');
			$(this).parent().addClass('active');
			$('.descr').hide();
			$('#'+var_nameBlock).show();
		}
	});

	$('.read-more').click(function() {
		if (! $(this).hasClass('active')) {
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

	$('.stars-li input').each(function() {
		if ($(this).attr('checked') == 'checked') {
			$(this).next().addClass('active');
		}
	});

	$('.stars-li label').click(function() {
		if(! $(this).hasClass('active')){
			$(this).addClass('active');
		}
		else {
			$(this).removeClass('active');
		}

	});
	var heCal = $('.calendarSlide').height();
	$('.calendarSlide').css('top','-'+heCal+'px');

	$('.input-path').click(function() {
		$('.calendarSlide').animate({'top' :  '0px'}, 400);
	});

/* НА ГЛАВНОЙ СТРАНИЦЕ ОТВЕЧАЕТ ЗА НАЖАТИЕ НА СТАР ИЗ ГОРОДА! */
	$('.cityStart .to a').click(function() {
		var var_parent = $(this).parent().parent();
		var var_parentUp = var_parent.parent();
		var_parentUp.find('.from').addClass('overflow').animate({'width':'125px'}, 300);
		var_parent.find('.startInputTo').show();
		var_parent.animate({'width' : '261px'}, 300, function() {
			var_parent.find('.startInputTo').find('input').focus();
		});
	});
	
	
	$('.board-content .from input').click(function() {
		if($(this).parent().hasClass('overflow')) {
			$(this).parent().animate({'width' : '271px'},300,function() {
				$(this).removeClass('overflow');
			});
			$('.cityStart').animate({'width' : '115px'}, 300);
			$('.cityStart').find('.startInputTo').animate({'opacity' : '1'},300, function() {
				$(this).hide();
			});
		}
	});
  }
});
