var var_widthMAX = 1390;
var var_widthMID = 1290;
var var_widthMIN = 1000;

var var_valueMAX = var_widthMAX - var_widthMID;
var var_valueMIN = var_widthMID - var_widthMIN;

var var_widthLeftBlockMAX = 295;
var var_widthLeftBlockMID = 295;
var var_widthLeftBlockMIN = 255;

var var_widthMiddleBlockOneMAX = 935;
var var_widthMiddleBlockMAX = 855;
var var_widthMiddleBlockMID = 755;
var var_widthMiddleBlockMIN = 585;

var var_widthFilterMAX = 240;
var var_widthFilterMID = 240;
var var_widthFilterMIN = 160;

var var_paddingLeftMAX = 32;
var var_paddingLeftMID = 32;
var var_paddingLeftMIN = 16;

var var_paddingRightSlideMAX = 305;
var var_paddingRightSlideMID = 305;
var var_paddingRightSlideMIN = 65;

var var_paddingLeftTelefonMAX = 250;
var var_paddingLeftTelefonMID = 250;
var var_paddingLeftTelefonMIN = 220;

var var_widthMainBlockMAX = 695;
var var_widthMainBlockMIN = 530;


function ResizeCenterBlock() {
    console.log("CENTER")
	var block = $('.center-block');
	var isset = block.length;
	if (isset) {
		var var_leftBlock = $('.left-block');
		var var_head = $('.head');
		var var_mainBlock = block.find('.main-block');
		var var_content = block.find('.main-block').find('#content');
		var var_filterBlock = block.find('.filter-block');
		var var_logoBlock = block.find('.logo');
		var var_aboutBlock = block.find('.about');
		var var_slideBlock = $('.slide-turn-mode');
		var var_telefonBlock = $('.telefon');
		var var_ticketsItems = $('.ticket-content');
		var var_recomendedItems = $('.head-content');
		var var_hotelItems = $('.hotels-tickets');
		var var_calendarGridVoyanga = $('.calendarGridVoyanga');
		var var_descrItems = $('#descr');
		var widthLeftBlock,
			widthMainBlock,
			widthFilterBlock,
			paddingLeftLogo = 32,
			leftTopPadding,
			paddingRightSlide,
			paddingLeftTel,
			marginLeftMain,
			marginLeftFilter,
			marginLeftMainBlock,
			marginRightMainBlock,
			marginRightFilterBlock,
			marginLeftLeftBlock,
			var_margin,
			marginRightContent,
			marginLeftContent,
			widthContent,
			var_widthDescrLeft,
			var_widthStreet,
			widthLogin;

		var widthBlock = block.width();
		var heightHead = var_head.height();
		var pos = block.eq(0).offset();

		var var_leftBlockIsset = var_leftBlock.length > 0 && var_leftBlock.is(':visible');
		var var_mainBlockIsset = var_mainBlock.length > 0 && var_mainBlock.is(':visible');
		var var_filterBlockIsset = var_filterBlock.length > 0 && var_filterBlock.is(':visible');
		var var_calendarGridVoyangaIsset = var_calendarGridVoyanga.length > 0 && var_calendarGridVoyanga.is(':visible');

		var var_descrIsset = var_descrItems.length > 0 && var_descrItems.is(':visible');

		if (! var_leftBlockIsset &&  ! var_filterBlockIsset && var_mainBlockIsset) {
			if (widthBlock >= var_widthMAX) {
				widthMainBlock = var_widthMiddleBlockOneMAX;
				marginLeftMainBlock = 'auto';
				marginRightMainBlock = 'auto';

				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;
			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {
				widthMainBlock = var_widthMiddleBlockOneMAX;
				marginLeftMainBlock = 'auto';
				marginRightMainBlock = 'auto';

				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				widthMainBlock = var_widthMiddleBlockOneMAX;
				marginLeftMainBlock = 'auto';
				marginRightMainBlock = 'auto';

				paddingLeftLogo = Math.floor(var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN))) );
				paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
				paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );
			}
		}
		else if (! var_leftBlockIsset &&  var_filterBlockIsset && var_mainBlockIsset) {
			if (widthBlock >= var_widthMAX) {
				widthMainBlock = var_widthMiddleBlockMAX;
				widthFilterBlock = var_widthFilterMAX;
				var_margin = Math.floor((widthBlock - (widthMainBlock + widthFilterBlock)) / 2)
				marginLeftMainBlock = var_margin;
				marginRightMainBlock = widthFilterBlock + var_margin;
				marginRightFilterBlock = var_margin;

				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;

				widthContent = var_widthMainBlockMAX;
				marginLeftContent = 'auto';
				marginRightContent = 'auto';
			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {
				widthMainBlock = Math.floor(var_widthMiddleBlockMID + ((widthBlock - var_widthMID) / 1));
				widthFilterBlock = var_widthFilterMID;
				var_margin = Math.floor((widthBlock - (widthMainBlock + widthFilterBlock)) / 2)
				marginLeftMainBlock = var_margin;
				marginRightMainBlock = widthFilterBlock + var_margin;
				marginRightFilterBlock = var_margin;

				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;

				widthContent = var_widthMainBlockMAX;
				marginLeftContent = 'auto';
				marginRightContent = 'auto';
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				widthFilterBlock = Math.floor(220 + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthFilterMID - 220))) );
				var_margin = 20;
				widthMainBlock = Math.floor((widthBlock  - widthFilterBlock) - (var_margin * 2));
				if (widthMainBlock > var_widthMiddleBlockMID) {
					widthMainBlock = var_widthMiddleBlockMID
				}
				var_margin = Math.floor((widthBlock - (widthMainBlock + widthFilterBlock)) / 2)
				marginLeftMainBlock = var_margin;
				marginRightMainBlock = widthFilterBlock + var_margin;
				marginRightFilterBlock = var_margin;

				paddingLeftLogo = Math.floor(var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN))) );
				paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
				paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );

				widthContent = var_widthMainBlockMAX;
				marginLeftContent = 'auto';
				marginRightContent = 'auto';
			}
		}

		else if (var_leftBlockIsset &&  var_filterBlockIsset && var_mainBlockIsset) {
			if (widthBlock >= var_widthMAX) {
				widthLeftBlock = var_widthLeftBlockMAX;
				widthMainBlock = var_widthMiddleBlockMAX;
				widthFilterBlock = var_widthFilterMAX;
				var_margin = Math.floor((widthBlock - (widthMainBlock + widthFilterBlock + widthLeftBlock)) / 2);
				marginLeftMainBlock = widthLeftBlock;
				marginRightMainBlock = widthFilterBlock;
				marginRightFilterBlock = 0;
				marginLeftLeftBlock = 0;

				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;

				widthContent = var_widthMainBlockMAX;
			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {
				widthLeftBlock = var_widthLeftBlockMID;
				widthMainBlock = Math.floor(var_widthMiddleBlockMID + ((widthBlock - var_widthMID) / 1));
				widthFilterBlock = var_widthFilterMID;
				marginLeftMainBlock = widthLeftBlock;
				marginRightMainBlock = widthFilterBlock;
				marginRightFilterBlock = 0;
				marginLeftLeftBlock = 0;

				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;

				widthContent = var_widthMainBlockMAX;
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				widthLeftBlock = Math.floor(var_widthLeftBlockMIN + ( (widthBlock - var_widthMIN) / (var_valueMIN / (var_widthLeftBlockMID - var_widthLeftBlockMIN))) );
				widthMainBlock = Math.floor(var_widthMiddleBlockMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthMiddleBlockMID - var_widthMiddleBlockMIN))) );
				widthFilterBlock = Math.floor(var_widthFilterMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthFilterMID - var_widthFilterMIN))) );

				paddingLeftLogo = Math.floor(var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN))) );
				paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
				paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );

				marginLeftMainBlock = widthLeftBlock;
				marginRightMainBlock = widthFilterBlock;
				marginRightFilterBlock = 0;
				marginLeftLeftBlock = 0;

				widthContent = Math.floor(var_widthMainBlockMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthMainBlockMAX - var_widthMainBlockMIN))) );
			}
		}

		else if (var_leftBlockIsset && var_mainBlockIsset &&  ! var_filterBlockIsset ) {
			if (widthBlock >= var_widthMAX) {
				widthLeftBlock = var_widthLeftBlockMAX;
				widthMainBlock = var_widthMiddleBlockOneMAX;
				var_margin = 80;
				marginRightMainBlock = var_margin;
				marginLeftMainBlock = widthLeftBlock + var_margin;
				marginLeftLeftBlock = 0;

				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;

				marginLeftContent = 0;
				widthContent = widthMainBlock - marginLeftContent;
				marginRightContent = 0;
				var_widthDescrLeft = 587;
				var_widthStreet = 'auto'
			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {
				widthMainBlock = Math.floor(910 + ( (widthBlock - var_widthMID) / (var_valueMAX / (935 - 910))) );
				widthLeftBlock = var_widthLeftBlockMID;
				var_margin = Math.floor(30 + ( (widthBlock - var_widthMID) / (var_valueMAX / (80 - 30))) );
				marginRightMainBlock = var_margin;
				marginLeftMainBlock = widthLeftBlock + var_margin;
				marginLeftLeftBlock = 0;

				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;

				marginLeftContent = 0;
				widthContent = widthMainBlock - marginLeftContent;

				marginRightContent = 0;
				var_widthDescrLeft = Math.floor(557 + ((widthBlock - var_widthMID) / (var_valueMAX / (587 - 557))) );
				var_widthStreet = 'auto'
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				widthLeftBlock = Math.floor(var_widthLeftBlockMIN + ( (widthBlock - var_widthMIN) / (var_valueMIN / (var_widthLeftBlockMID - var_widthLeftBlockMIN))) );

				widthMainBlock = Math.floor(685 + ( (widthBlock - var_widthMIN) / (var_valueMIN / (910 - 685))) );

				var_margin = 30;
				marginRightMainBlock = var_margin;
				marginLeftMainBlock = widthLeftBlock + var_margin;
				marginLeftLeftBlock = 0;

				paddingLeftLogo = Math.floor(var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN))) );
				paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
				paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );

				marginLeftContent = 0;
				widthContent = widthMainBlock - marginLeftContent;
				marginRightContent = 0;

				var_widthDescrLeft = Math.floor(335 + ((widthBlock - var_widthMIN) / (var_valueMIN / (557 - 335))) );

				var_widthStreet = '210px';
			}
		}
		else {
			if (widthBlock >= var_widthMAX) {


				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;

			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {


				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;


			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {


				paddingLeftLogo = Math.floor(var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN))) );
				paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
				paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );

			}
		}

		/*===*/
		if (var_mainBlockIsset) {
			var_mainBlock.css('width', widthMainBlock+'px').css('margin-left', marginLeftMainBlock+'px').css('margin-right', marginRightMainBlock+'px');
			var_content.css('width', widthContent+'px').css('margin-left', marginLeftContent +'px').css('margin-right', marginRightContent +'px');
		}
		if (var_filterBlockIsset) {
			var_filterBlock.css('width', widthFilterBlock+'px').css('margin-right', marginRightFilterBlock+'px');
		}
		if (var_leftBlockIsset) {
			var_leftBlock.css('width', widthLeftBlock+'px').css('margin-left', marginLeftLeftBlock+'px');
		}
		/* CALENDARE RESIZE */
		if (var_calendarGridVoyangaIsset) {
			$('.innerCalendar, #voyanga-calendar').css('width', widthBlock+'px')
			var_calendarGridVoyanga.css('width', (widthBlock+16)+'px');
			$('.weekDaysVoyangaInner').css('width', (widthBlock+16)+'px');
		}
		/* END CALENDARE RESIZE */
		if (var_descrIsset) {
			$('#descr').find('.photo-slide-hotel').css('width', var_widthDescrLeft+'px');
			$('#descr').find('.left').find(".descr-text .text").dotdotdot({watch: 'window'});
			$('#content .place-buy .street').css('width', var_widthStreet);

		}
		if ($('.description .text').length > 0 && $('.description .text').is(':visible')) {
			$(".description .text").dotdotdot({watch: 'window'});
		}
		/*===*/
		var_logoBlock.css('left', paddingLeftLogo+'px');
		var_aboutBlock.css('left', (122 + paddingLeftLogo)+'px');
		var_slideBlock.css('right', paddingRightSlide +'px');
		var_leftBlock.find('.left-content').css('margin-left', paddingLeftLogo+'px');
		var_telefonBlock.css('left', paddingLeftTel+'px');

		if (widthContent < 695) {
			var mathWidthRicket = Math.floor(253 + ((widthBlock - var_widthMIN) / (var_valueMIN / (318 - 253))) );
			$('.recommended-ticket').css('width', mathWidthRicket+'px');
			$('.recommended-ticket').find('.ticket-items').addClass('small');
			var_content.find('h1').find('span').hide();
			var_ticketsItems.find('.ticket-items').addClass('small');
			var_hotelItems.addClass('small');
			/*
			var mathWidthRicket = Math.floor(var_widthTicket * 0.393);
			var_recomendedItems.find('.recommended-ticket').css('width', (253 + mathWidthRicket)+'px');

			var_recomendedItems.css('width', var_allWidthContent+'px');
			var_recomendedItems.*/
		}
		else {
			$('.recommended-ticket').find('.ticket-items').removeClass('small');
			$('.recommended-ticket').css('width', '318px');
			var_content.find('h1').find('span').show();
			var_ticketsItems.find('.ticket-items').removeClass('small');
			var_hotelItems.removeClass('small');
			/*
			var_recomendedItems.find('.recommended-ticket').css('width', '318px');
			var_recomendedItems.css('width', var_widthMainBlockMAX+'px');
			var_recomendedItems.find('h1').find('span').show();*/
		}

		/*
		if(var_hotelItems.length > 0 && var_hotelItems.is(':visible')) {
			if (var_ticketsItems.width() < 650) {
				var_hotelItems.addClass('small');
			}
			else {
				var_hotelItems.removeClass('small');
			}
		}
		*/
		if (widthBlock <= var_widthMIN) {
			$('body').css('overflow-x','scroll');
		}
		else {
			$('body').css('overflow-x', 'hidden');
			$('body').css('overflow-y', 'hidden');
		}

		resizeLeftStage();
		resizeMainStage();
	}
}
function smallTicketHeight() {
    if ($('.recommended-ticket').length > 0 && $('.recommended-ticket').is(':visible')) {
	var var_recomendedContent = $('.recomended-content');
	var var_oneHeight = var_recomendedContent.find('.recommended-ticket .ticket-items').height();
	var var_twoHeight = var_recomendedContent.find('.prices-of-3days .ticket').height();

	if ((var_oneHeight - 19)!= var_twoHeight) {
	    var var_recomendedItems = var_recomendedContent.find('.recommended-ticket .ticket-items');
	    var heightOneTicket = var_recomendedContent.find('.recommended-ticket')[0].clientHeight;
	    heightOneTicket += 2;
	    var_recomendedItems.css('height', heightOneTicket +'px');
	    var_recomendedContent.find('.prices-of-3days .ticket').css('height', (heightOneTicket - 19) +'px');
	    var heightTwoTicket = $('.recomended-content').find('.prices-of-3days')[0].clientHeight;
	    if ($('.recommended-ticket').find('.two-way').is(':visible')) {
		heightTwoTicket = ((heightOneTicket - 35) - 17) / 2;				
	    } else {
		heightTwoTicket = ((heightOneTicket - 35) - 17);
	    }
	    heightTwoTicket = Math.floor(heightTwoTicket);
	    var_recomendedContent.find('.prices-of-3days .ticket .schedule-of-prices').css('height', heightTwoTicket +'px');
	    var heightGraf = heightTwoTicket - 45;
	    
	    if (heightGraf < 100) {
		var_recomendedContent.find('.prices-of-3days .ticket .schedule-of-prices li .chart').css('height', heightGraf +'px');
	    }
	}
    }
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


function resizeLeftStage() {
	var leftStage = $('.left-block');
	var leftWidth = leftStage.width();
	var leftDate = leftStage.find('.date');
	var startPosition = 150;
	var leftPaddingDate = 215;
	leftPaddingDate = (leftWidth - leftPaddingDate);
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

}
function resizeMainStage() {
	var var_this = $('.prices-of-3days');

	var var_widthChange = $('.recommended-ticket').width();
	if (var_widthChange < 318) {
		var var_widthOneLi = var_widthChange / 7;
			var_widthOneLi = Math.floor(var_widthOneLi);
		var_widthChange = var_widthOneLi * 7;
		var_this.find('.schedule-of-prices').css('width', var_widthChange+'px');
		var_this.find('.schedule-of-prices li').css('width', var_widthOneLi+'px');
		var_this.css('width', var_widthChange+'px');
		var_this.find('.total-td .text').hide();
		var_this.find('.total-td').css('margin-left','-15px');
		var_this.find('.look-td').css('margin-right','-15px');
	}
	else {
		var_this.find('.schedule-of-prices').css('width', '318px');
		var_this.find('.schedule-of-prices li').css('width', '45px');
		var_this.css('width', '318px');
		var_this.find('.total-td').css('margin-left','0px');
		var_this.find('.look-td').css('margin-right','0px');
		var_this.find('.total-td .text').show();
	}

	if (var_widthChange < 290 && var_widthChange > 280) {
		var_this.find('.schedule-of-prices li').find('.price').css('left','-1px');
	}
	else if (var_widthChange < 280 && var_widthChange > 270) {
		var_this.find('.schedule-of-prices li').find('.price').css('left','-2px');
	}
	else if (var_widthChange < 270 && var_widthChange > 260) {
		var_this.find('.schedule-of-prices li').find('.price').css('left','-3px');
	}
	else if (var_widthChange < 260 && var_widthChange > 255) {
		var_this.find('.schedule-of-prices li').find('.price').css('left','-4px');
	}
	else if (var_widthChange < 255) {
		var_this.find('.schedule-of-prices li').find('.price').css('left','-5px');
	}
	else {
		var_this.find('.schedule-of-prices li').find('.price').css('left','0px');
	}
}

function AlphaBackground() {
	$('.my-trip-list').find('.items').find('.path').each(function() {
		$(this).append('<div class="alpha"></div>');
	});
}


function ResizeAvia() {
    ResizeCenterBlock();
    smallTicketHeight();

}

function ResizeFun() {
    ResizeAvia();
//    loginResize();
}
$(window).load(AlphaBackground);

