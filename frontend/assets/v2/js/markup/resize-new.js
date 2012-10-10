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

var var_paddingLeftMAX = 12;
var var_paddingLeftMID = 12;
var var_paddingLeftMIN = 12;

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
		var var_calendarGridVoyanga = $('.calenderWindow');
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
		var var_leftBlockIsset = var_leftBlock.length > 0 && var_leftBlock.is(':visible');
		var var_mainBlockIsset = var_mainBlock.length > 0 && var_mainBlock.is(':visible');
		var var_filterBlockIsset = var_filterBlock.length > 0 && var_filterBlock.is(':visible');
		var var_calendarGridVoyangaIsset = var_calendarGridVoyanga.length > 0 && var_calendarGridVoyanga.is(':visible');

		var var_descrIsset = var_descrItems.length > 0 && var_descrItems.is(':visible');

		if (! var_leftBlockIsset &&  ! var_filterBlockIsset && var_mainBlockIsset) {
			//console.log("THIS IS === 1 === IF ELSE");
			if (widthBlock >= var_widthMAX) {
				widthMainBlock = var_widthMiddleBlockOneMAX;
				marginLeftMainBlock = 'auto';
				marginRightMainBlock = 'auto';

				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;
				
				widthContent = widthMainBlock;
				
				paddingRightSlide += 165;
			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {
				widthMainBlock = var_widthMiddleBlockOneMAX;
				marginLeftMainBlock = 'auto';
				marginRightMainBlock = 'auto';

				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;
				
				widthContent = widthMainBlock;
				
				paddingRightSlide += 165;
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				widthMainBlock = var_widthMiddleBlockOneMAX;
				marginLeftMainBlock = 'auto';
				marginRightMainBlock = 'auto';

				paddingLeftLogo = Math.floor(var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN))) );
				paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
				paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );
				
				widthContent = widthMainBlock;
				
				paddingRightSlide += 100;
			}
		}
		else if (! var_leftBlockIsset &&  var_filterBlockIsset && var_mainBlockIsset) {
			//console.log("THIS IS === 2 === IF ELSE");
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
				
				paddingRightSlide += 165;
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
				
				paddingRightSlide += 165;
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
				
				paddingRightSlide += 100;
			}
		}

		else if (var_leftBlockIsset &&  var_filterBlockIsset && var_mainBlockIsset) {
			//console.log("THIS IS === 3 === IF ELSE");
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
				
				paddingRightSlide += 165;
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
				
				paddingRightSlide += 165;
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
				
				paddingRightSlide += 100;
			}
		}

		else if (var_leftBlockIsset && var_mainBlockIsset &&  ! var_filterBlockIsset ) {
			//console.log("THIS IS === 4 === IF ELSE"); 
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
				
				paddingRightSlide += 165;
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
				
				paddingRightSlide += 165;
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
				
				paddingRightSlide += 100;
			}
		}
		else {
			//console.log("THIS IS === 5 === IF ELSE");
			if (widthBlock >= var_widthMAX) {


				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;
				
				paddingRightSlide += 165;

			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {


				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;

				paddingRightSlide += 165;	
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				paddingLeftLogo = Math.floor(var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN))) );
				paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
				paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );
				
				paddingRightSlide += 100;
			}
		}
		if (marginLeftMainBlock != 'auto') {
				marginLeftMainBlock = marginLeftMainBlock+'px';
		}
		if (marginRightMainBlock != 'auto') {
			marginRightMainBlock = marginRightMainBlock+'px';
		}
		if (marginLeftContent != 'auto') {
			marginLeftContent = marginLeftContent +'px';
		}
		if (marginRightContent != 'auto') {
			marginRightContent = marginRightContent +'px';
		}
		if (marginRightFilterBlock != 'auto') {
			marginRightFilterBlock = marginRightFilterBlock +'px';
		}
		if (marginLeftLeftBlock != 'auto') {
			marginLeftLeftBlock = marginLeftLeftBlock +'px';
		}
		/*===*/
		if (var_mainBlockIsset) {
			
			var_mainBlock.css('width', widthMainBlock+'px').css('margin-left', marginLeftMainBlock).css('margin-right', marginRightMainBlock);
			var_content.css('width', widthContent+'px').css('margin-left', marginLeftContent).css('margin-right', marginRightContent);
		}
		if (var_filterBlockIsset) {
			var_filterBlock.css('width', widthFilterBlock+'px').css('margin-right', marginRightFilterBlock);			
		}
		if (var_leftBlockIsset) {
			var_leftBlock.css('width', widthLeftBlock+'px').css('margin-left', marginLeftLeftBlock);
		}
		/* CALENDARE RESIZE */
		if (var_calendarGridVoyangaIsset) {
			$('.innerCalendar, #voyanga-calendar').css('width', widthBlock+'px')
			//var_calendarGridVoyanga.css('width', (widthBlock+16)+'px');
			//$('.weekDaysVoyangaInner').css('width', (widthBlock+16)+'px');
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

		if (widthContent < 690) {
			var mathWidthRicket = Math.floor(253 + ((widthBlock - var_widthMIN) / (var_valueMIN / (318 - 253))) );
			$('.recommended-ticket').css('width', mathWidthRicket+'px');
			$('.recommended-ticket').find('.ticket-items').addClass('small');
			var_content.find('h1').find('span').hide();
			var_ticketsItems.find('.ticket-items').addClass('small');
			var_hotelItems.addClass('small');
		}
		else {
			$('.recommended-ticket').find('.ticket-items').removeClass('small');
			$('.recommended-ticket').css('width', '318px');
			var_ticketsItems.find('.ticket-items').removeClass('small');
			var_hotelItems.removeClass('small');
		}
		resizeLeftStage();
		resizeMainStage();
	}
	$(".second-path").focus(function () {
        $(this).select();
    }).mouseup(function(e){
        e.preventDefault();
    });
}
function smallTicketHeight() {
    if ($('.recommended-ticket').length > 0 && $('.recommended-ticket').is(':visible')) {
    
	var var_recomendedContent = $('.recomended-content');
	var var_recomendedItems = var_recomendedContent.find('.recommended-ticket .ticket-items .content');
	var var_oneHeight = var_recomendedItems.height();
	console.log(var_oneHeight);
	//var_recomendedContent.find('.prices-of-3days .ticket').css('height', var_oneHeight +'px');
	//var_recomendedItems.css('height', var_oneHeight +'px');
	
	var heightTwoTicket= 0;
	if ($('.two-way').css('display')!=='none') {
	    heightTwoTicket = (var_oneHeight - 32) / 2;				
	} else {
	    heightTwoTicket = (var_oneHeight - 32);
	}
	heightTwoTicket = Math.floor(heightTwoTicket);
	//console.log(heightTwoTicket);
	var_recomendedContent.find('.prices-of-3days .ticket .schedule-of-prices').css('height', heightTwoTicket +'px');
	var heightGraf = heightTwoTicket - 65;	
	var_recomendedContent.find('.prices-of-3days .ticket .schedule-of-prices li .chart').css('height', heightGraf +'px');
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
	var startPosition = 170;
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
    inTheTwoLines();
    smallTicketHeight();
    scrollValue('avia');
    scrollValue('hotel');
    CenterIMGResize();
    ifHeightMinAllBody();
    showMiniPopUp();
}

function ResizeFun() {
    ResizeAvia();
}
function scrolShowFilter() {
	$('#scroll-pane').each(
		function()
		{
			$(this).jScrollPane(
				{
					showArrows: $(this).is('.arrow')
				}
			);
			var api = $(this).data('jsp');
			var throttleTimeout;

			$(window).bind(
				'resize',
				function()
				{
					if ($.browser.msie) {
						// IE fires multiple resize events while you are dragging the browser window which
						// causes it to crash if you try to update the scrollpane on every one. So we need
						// to throttle it to fire a maximum of once every 50 milliseconds...
						if (!throttleTimeout) {
							throttleTimeout = setTimeout(
								function()
								{
									api.reinitialise();
									throttleTimeout = null;
								},
								300
							);
						}
					} else {
						api.reinitialise();
					}
				}
			);
			$('body').bind(
				'resize',
				function()
				{
					console.log("СВЕРШИЛОСЬ!!!!");
				}
			);
			$(window).bind(
				'scroll',
				function()
				{
                    var throttleTimeout;
                    if (!throttleTimeout) {
                        throttleTimeout = setTimeout(
                            function()
                            {
                                api.reinitialise();
                                throttleTimeout = null;
                            },
                            300
                        );
                    }
                }
			);
            $('.all-list, .order-hide').live(
                'click',
                function()
                {	
                    var throttleTimeout;
                    if (!throttleTimeout) {
                        throttleTimeout = setTimeout(
                            function()
                            {
                                api.reinitialise();
                                throttleTimeout = null;
                            },
                            100
                        );
                    }
                }
            );
            $('.div-filter').live(
                'mouseup',
                function()
                {	
                    var throttleTimeout;
                    if (!throttleTimeout) {
                        throttleTimeout = setTimeout(
                            function()
                            {
                                api.reinitialise();
                                throttleTimeout = null;
                            },
                            1000
                        );
                    }
                }
            );
		}
	)
}
function OneWidthEquelTwoWidth() {
	if ($('.jspPane').width() == $('.scrollBlock').width() ) {
		$('.slide-filter.first').css('padding-right','21px');
	}
	else {
		$('.slide-filter.first').css('padding-right','30px');
	}
}
function scrollValue(what) {
	var filterContent = $('.filter-content.'+what)
	var var_marginTopSubHead = $('.sub-head').css('margin-top');
	var var_scrollValueTop = $(window).scrollTop();
	var var_heightWindow = $(window).height();
	if (what == 'avia') {
		var var_topFilterContent = 73;
		if ($('.sub-head').css('margin-top') != '-67px') {
			var diffrentScrollTop = 179;
			
		}
		else {
			var diffrentScrollTop = 110;
			
		}
	}
	else {
		var var_topFilterContent = 0;
		if ($('.sub-head').css('margin-top') != '-67px') {
			var diffrentScrollTop = 131;
			
		}
		else {
			var diffrentScrollTop = 61 ;
			
		}
	}
	var var_heightWindow = $(window).height();
	var var_heightContent = $('#content').height();
	console.log(var_scrollValueTop);
	if (var_scrollValueTop == 0) {
		filterContent.css('position','relative').css('top','auto').css('bottom','auto');
	}
	else if (var_scrollValueTop > 0 && var_scrollValueTop < diffrentScrollTop ) {
		filterContent.css('position','relative').css('top','auto').css('bottom','auto');
		if (var_heightContent < var_heightWindow) {
			$('.innerFilter').css('height', (var_heightWindow - 210)+'px');
		}
		else {
			$('.innerFilter').css('height', '100%');
		}
	}
	else if (var_scrollValueTop > diffrentScrollTop) {
		
		if (var_scrollValueTop > (($('.wrapper').height() - var_heightWindow) - var_topFilterContent) && var_scrollValueTop != 0) {
			filterContent.css('position','fixed').css('bottom','var_topFilterContentpx').css('top','auto');
			$('.innerFilter').css('height', (var_heightWindow - (var_topFilterContent - (($('.wrapper').height() - var_heightWindow)-var_scrollValueTop))) +'px');
		}
		else if (var_scrollValueTop > (($('.wrapper').height() - var_heightWindow) - var_topFilterContent) && var_scrollValueTop == 0) {
			filterContent.css('position','fixed').css('bottom','auto').css('top','auto');
			$('.innerFilter').css('height', (var_heightWindow - (var_topFilterContent - (($('.wrapper').height() - var_heightWindow)-var_scrollValueTop))) +'px');
		}
		else {
			filterContent.css('position','fixed').css('top','-'+var_topFilterContent+'px').css('bottom','auto');
			$('.innerFilter').css('height', var_heightWindow +'px');
		}
		
	}		
}
$(window).load(AlphaBackground);

$(window).load(function() {
	$(window).scroll(function() {
	scrollValue('avia');
	scrollValue('hotel');
	});
});

function ifHeightMinAllBody() {
	$('#content').css('height','auto');
	var var_heightWindow = $(window).height();
	var var_heightContent = $('#content').height();
	
	if (var_heightContent < var_heightWindow) {
		$('#content').css('height', (var_heightWindow - 168) +'px');
		$('.innerFilter').css('height', (var_heightWindow - 210)+'px');
		//$('body').css('height', var_heightWindow +'px');
		console.log("НУ ЧТО? = "+var_heightContent+ " x "+ (var_heightWindow - 168));
	}
	else {
		$('#content').css('height', 'auto');
		$('.innerFilter').css('height', '100%');
		//$('body').css('height', 'auto');
		console.log("И ТО? = "+var_heightContent+ " x "+ (var_heightWindow - 168));
		
	}
	
}

function showMiniPopUp() {
	var miniPopUp = '<div class="miniPopUp"></div>';
	$('.conditionCancel').hover(function(e) {
		console.log(e);
		var widthThisElement = $(this).width();
		$('body').append(miniPopUp);
		$('.miniPopUp').text($(this).attr('rel')).css('left', (e.pageX - (widthThisElement / 2))+'px').css('top', (e.pageY + 10)+'px');
	}, function() {
		$('.miniPopUp').remove();
	});
}

