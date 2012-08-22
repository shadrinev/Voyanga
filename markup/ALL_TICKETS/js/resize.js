function ResizeCenterBlock() {
	var block = $('.center-block');
	var isset = block.length;
	if (isset) {
		var widthLeftBlock, widthMainBlock, widthFilterBlock, paddingLeftLogo = 32, leftTopPadding, paddingRightSlide, paddingLeftTel, widthLogin;
		var widthWindow = $(window).width();
		var heightWindow = $(window).height();
		var widthBlock = block.width();
		var heightHead = $('.head').height();
		var heightFootTab = $('.foot-tab').height();
		var pos = $('.center-block').eq(0).offset();
		
		if ($('.left-block').length > 0 && $('.left-block').is(':visible')) {			
			if (widthBlock >= 1390) {
				widthLeftBlock = 295;
				widthMainBlock = 855;
				widthFilterBlock = 240;
				paddingLeftLogo = 32;
				paddingRightSlide = 305;
				paddingLeftTel = 250;
			}
			else if (widthBlock < 1390 && widthBlock >=1290) {
				widthLeftBlock = 295;
				widthMainBlock = 755 + ((widthBlock - 1290) / 1);
				widthMainBlock = Math.floor(widthMainBlock);
				widthFilterBlock = 240;
				paddingLeftLogo = 32;
				paddingRightSlide = 305;
				paddingLeftTel = 250;
			}
			else if (widthBlock < 1290 && widthBlock >=1000) {
				widthLeftBlock = 255 + ((widthBlock - 1000) / 7.25);
				widthLeftBlock = Math.floor(widthLeftBlock);
				widthMainBlock = 585 + ((widthBlock - 1000) / 1.7);
				widthMainBlock = Math.floor(widthMainBlock);
				widthFilterBlock = 160 + ((widthBlock - 1000) / 3.625);
				widthFilterBlock = Math.floor(widthFilterBlock);
				paddingLeftLogo = 16 + ((widthBlock - 1000) / 18.125);
				paddingLeftLogo = Math.floor(paddingLeftLogo);
				paddingRightSlide = 65 + ((widthBlock - 1000) / 1.2);
				paddingRightSlide = Math.floor(paddingRightSlide);
				paddingLeftTel = 220 + ((widthBlock - 1000) / 9.6);
				paddingLeftTel = Math.floor(paddingLeftTel);
			}
						
			block.find('.main-block').css('margin-left', widthLeftBlock+'px');
			$('.left-block').css('width', widthLeftBlock+'px');
		}
		else {
			if (widthBlock >= 1390) {
				widthMainBlock = 1150;
				widthFilterBlock = 240;
				paddingRightSlide = 305;
				paddingLeftTel = 250;
			}
			else if (widthBlock < 1390 && widthBlock >=1290) {
				widthMainBlock = 1050 + ((widthBlock - 1290) / 1);
				widthMainBlock = Math.floor(widthMainBlock);
				widthFilterBlock = 240;
				paddingRightSlide = 305;
				paddingLeftTel = 250;
			}
			else if (widthBlock < 1290 && widthBlock >=1000) {
				widthMainBlock = 840 + ((widthBlock - 1000) / 1.38);
				widthMainBlock = Math.floor(widthMainBlock);
				widthFilterBlock = 160 + ((widthBlock - 1000) / 3.625);
				widthFilterBlock = Math.floor(widthFilterBlock);
				paddingRightSlide = 65 + ((widthBlock - 1000) / 1.2);
				paddingRightSlide = Math.floor(paddingRightSlide);
				paddingLeftTel = 220 + ((widthBlock - 1000) / 9.6);
				paddingLeftTel = Math.floor(paddingLeftTel);
			}
		}
		if (widthBlock <= 1001) {
			$('body').css('overflow-x','scroll');
		}
		else {
			$('body').css('overflow-x', 'hidden');
			$('body').css('overflow-y', 'hidden');
		}
		block.find('.main-block').css('width', widthMainBlock+'px');
		block.find('.filter-block').css('width', widthFilterBlock+'px');
		block.find('.logo').css('left', paddingLeftLogo+'px');
		block.find('.about').css('left', (122 + paddingLeftLogo)+'px');
		$('.slide-turn-mode').css('right', paddingRightSlide +'px');
		$('#telefon').css('left', paddingLeftTel+'px');
		$('.left-block').find('.left-content').css('margin-left', paddingLeftLogo+'px');
		
		SubHeadIsset();
		resizeLeftStage()
		resizeMainStage();
	}
}
function ResizeFun() {
	ResizeCenterBlock();
	loginResize();
	$(window).resize(ResizeCenterBlock);
	$(window).resize(loginResize);
}
function loginResize() {
	var widthWindow = $(window).width();
	if (widthWindow > 1160) {

			$('.login-window a .text').show();
			$('.login-window').css('width','165px');
	
	}
	else {
		$('.login-window a .text').hide();
			$('.login-window').css('width','40px');
	}
	
}
function SubHeadIsset() {
	if ($('.left-block').length > 0 && $('.left-block').is(':visible')) {
		var heightHead = $('.head').height();
		var leftHeight = $('.left-block').height();
		var heightWindow = $(window).height();
		var scrollWin = $('.wrapper').scrollTop();
		//console.log(scrollWin);
		if ($('.sub-head').length > 0 && $('.sub-head').is(':visible')) {
			subHeadHeight = $('.sub-head').height();
			paddingTopLeft = subHeadHeight + heightHead;
		}
		else {
			subHeadHeight = 0;
			paddingTopLeft = heightHead;
		}
		if (scrollWin >= 0 && scrollWin <= (subHeadHeight + heightHead)) {
			paddingTopLeft = (heightHead  + subHeadHeight) - scrollWin;
		}
		else {
			paddingTopLeft = 0;
		}
		heightLeftBlock = heightWindow - paddingTopLeft;
		heightLeftBlock = FootTabIsset(heightLeftBlock);
		//console.log(heightLeftBlock);
		/*$('.left-block').css('top', paddingTopLeft +'px').css('height', heightLeftBlock+'px')*/;
	}
}
function FootTabIsset(heightLeftBlock) {
	var leftHeight = heightLeftBlock;
	if ($('.foot-tab').length > 0 && $('.foot-tab').is(':visible')) {
		var heightFootTab = $('.foot-tab').height();
		var heightLeftBlock = leftHeight - heightFootTab;
	}
	else {
		var heightLeftBlock = leftHeight - 0;
	}
	return heightLeftBlock;
}

function ScrollWindow() {
	$('.wrapper').scroll(function() {
		SubHeadIsset();
	});
}
function resizeLeftStage() {
	var leftStage = $('.left-block');
	var leftWidth = leftStage.width();
	var leftDate = leftStage.find('.date');
	var startPosition = 150;
	var leftPaddingDate = 215;
	leftPaddingDate = (leftWidth - leftPaddingDate);
	console.log(leftPaddingDate);
	if (leftPaddingDate < 75) {
		var leftPadding = leftPaddingDate / 1.5 ;
		leftPadding = leftPadding + 100;
		leftStage.find('.alpha').show();
	}
	else {
		leftPadding = startPosition;
		leftStage.find('.alpha').hide();
	}
	if (leftPadding < 125) {
		leftStage.find('.path').css('width', '125px');
	}
	else {
		leftStage.find('.path').css('width', leftPadding+'px');
	}
	console.log(leftPadding);
	
}
function resizeMainStage() {
	var main = $('.main-block');
	var recommendedTicket = main.find('.recommended-ticket');
	var ticketContent = main.find('.ticket-content');
	var pricesOf3days = main.find('.prices-of-3days');
	
	var mainWidth = main.width();
	if (mainWidth < 760) {
		recommendedTicket.css('width', '250px').css('margin-right','30px');
		recommendedTicket.find('.how-long').addClass('small').find('div').hide();
		var allW = recommendedTicket.find('.date-time-city').width();
		var startW = recommendedTicket.find('.date-time-city .start').width();
		var finishW = recommendedTicket.find('.date-time-city .finish').width();
		recommendedTicket.find('.date-time-city .how-long').css('width', ((allW - (startW + finishW)) - 2)+'px');
		recommendedTicket.find('.btn-cost').find('span.text').hide();
		recommendedTicket.find('.btn-cost').css('width','110px').css('margin-left','19px');		
		recommendedTicket.find('.variation').css('width','100px');
		/* pricesOf3days */
		pricesOf3days.css('width','247px');
		pricesOf3days.find('.schedule-of-prices').addClass('small');
		pricesOf3days.find('.schedule-of-prices li .price').addClass('margin');
		pricesOf3days.find('.total-td').find('.text').hide();
		pricesOf3days.find('.total-td').css('margin-left', '-15px');
		pricesOf3days.find('.look-td').css('margin-right', '-10px');
		
		main.find('.content').css('width','530px');
		main.find('.content h1').find('span').hide();
	}
	else {
		recommendedTicket.css('width', '318px').css('margin-right','60px');
		recommendedTicket.find('.btn-cost').find('span.text').show();
		recommendedTicket.find('.btn-cost').css('width','185px').css('margin-left','12px');
		recommendedTicket.find('.how-long').removeClass('small').find('div').show();
		var allW = recommendedTicket.find('.date-time-city').width();
		var startW = recommendedTicket.find('.date-time-city .start').width();
		var finishW = recommendedTicket.find('.date-time-city .finish').width();
		recommendedTicket.find('.date-time-city .how-long').css('width', ((allW - (startW + finishW)) - 2)+'px');
		recommendedTicket.find('.variation').css('width','170px');
		/* pricesOf3days */
		pricesOf3days.css('width','317px');
		pricesOf3days.find('.schedule-of-prices').removeClass('small');
		pricesOf3days.find('.schedule-of-prices li .price').removeClass('margin');
		pricesOf3days.find('.total-td').find('.text').show();
		pricesOf3days.find('.total-td').css('margin-left', '0px');
		pricesOf3days.find('.look-td').css('margin-right', '0px');
		
		main.find('.content').css('width','695px');
		main.find('.content h1').find('span').show();
	}
}
function AlphaBackground() {
	$('.my-trip-list').find('.items').find('.path').each(function() {
		$(this).append('<div class="alpha"></div>');
	});
}
/*function ScrollOtherWin() {
	if (document.getElementById('Frame').addEventListener) {
	document.getElementById('Frame').addEventListener('DOMMouseScroll', wheel, false);
	}
	//для всех остальных
	document.getElementById('Frame').onmousewheel = wheel;

	function wheel(event) {
	   var wDelta = 0; // значение на сколько покрутилось колесо
	   // опять забота о кроссбраузерности
	   if (event.wheelDelta)  {
	        wDelta = event.wheelDelta/120;
	    }   else if (event.detail)   {     
	        wDelta = -event.detail/3;
	    }
	    // тут обрабатываем результат, например:
	   console.log(wDelta);
	   // и заботимся о том, чтобы прокручивание колесика над элементом, не прокручивала скроллы страницы или еще что
	    if (event.preventDefault)
	    {
	        event.preventDefault();
	    }
	    event.returnValue = false;
	}
	
}*/
$(window).load(AlphaBackground);
$(window).load(ResizeFun);
$(window).load(ScrollWindow);
//$(window).load(ScrollOtherWin);