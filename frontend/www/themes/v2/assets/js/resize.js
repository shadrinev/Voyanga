var var_widthMAX = 1390;
var var_widthMID = 1290;
var var_widthMIN = 1000;

var var_valueMAX = var_widthMAX - var_widthMID;
var var_valueMIN = var_widthMID - var_widthMIN;

var var_widthLeftBlockMAX = 295;
var var_widthLeftBlockMID = 295;
var var_widthLeftBlockMIN = 255;

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
	var block = $('.center-block');
	var isset = block.length;
	if (isset) {
		var var_leftBlock = $('.left-block');
		var var_head = $('.head');
		var var_mainBlock = block.find('.main-block');
		var var_filterBlock = block.find('.filter-block');
		var var_logoBlock = block.find('.logo');
		var var_aboutBlock = block.find('.about');
		var var_slideBlock = $('.slide-turn-mode');
		var var_telefonBlock = $('.telefon');
		var var_ticketsItems = $('.ticket-content');
		var var_recomendedItems = $('.head-content');
		var var_hotelItems = $('.hotels-tickets');
		var widthLeftBlock, 
			widthMainBlock, 
			widthFilterBlock, 
			paddingLeftLogo = 32, 
			leftTopPadding, 
			paddingRightSlide, 
			paddingLeftTel,
			marginLeftMain,
			marginLeftFilter, 
			widthLogin;
			
		var widthWindow = $(window).width();
		var heightWindow = $(window).height();
		var widthBlock = block.width();
		var heightHead = var_head.height();
		var pos = block.eq(0).offset();
		
		if (var_leftBlock.length > 0 && var_leftBlock.is(':visible')) {
					
			if (widthBlock >= var_widthMAX) {
				widthLeftBlock = var_widthLeftBlockMAX;
				widthMainBlock = var_widthMiddleBlockMAX;
				widthFilterBlock = var_widthFilterMAX;
				paddingLeftLogo = var_paddingLeftMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;
			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {
				widthLeftBlock = var_widthLeftBlockMID;
				widthMainBlock = var_widthMiddleBlockMID + ((widthBlock - var_widthMID) / 1);
					widthMainBlock = Math.floor(widthMainBlock);
				widthFilterBlock = var_widthFilterMID;
				paddingLeftLogo = var_paddingLeftMID;
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				widthLeftBlock = var_widthLeftBlockMIN + ( (widthBlock - var_widthMIN) / (var_valueMIN / (var_widthLeftBlockMID - var_widthLeftBlockMIN) ) );
					widthLeftBlock = Math.floor(widthLeftBlock);	

				widthMainBlock = var_widthMiddleBlockMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthMiddleBlockMID - var_widthMiddleBlockMIN)) );
					widthMainBlock = Math.floor(widthMainBlock);
						
				widthFilterBlock = var_widthFilterMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthFilterMID - var_widthFilterMIN)) );
					widthFilterBlock = Math.floor(widthFilterBlock);
					
				paddingLeftLogo = var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN)) );
					paddingLeftLogo = Math.floor(paddingLeftLogo);
					
				paddingRightSlide = var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN)) );
					paddingRightSlide = Math.floor(paddingRightSlide);
					
				paddingLeftTel = var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN)));
					paddingLeftTel = Math.floor(paddingLeftTel);
			}
						
			var_mainBlock.css('margin-left', widthLeftBlock+'px');
			var_mainBlock.css('width', widthMainBlock+'px');
			
			var_leftBlock.css('width', widthLeftBlock+'px');
			var_filterBlock.css('width', widthFilterBlock+'px');
			
		}
		else {
			if (widthBlock >= var_widthMAX) {
				widthMainBlock = var_widthMAX;
				marginLeftMain = (var_widthMAX - var_widthMIN) / 2;
				widthFilterBlock = var_widthFilterMAX;
				marginLeftFilter = var_widthFilterMAX;
				paddingRightSlide = var_paddingRightSlideMAX;
				paddingLeftTel = var_paddingLeftTelefonMAX;
				paddingLeftLogo = var_paddingLeftMAX;
			}
			else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID) {
				widthMainBlock = widthBlock;
				marginLeftMain = (var_widthMAX - var_widthMIN) / 2;
					marginLeftMain = Math.floor(marginLeftMain);
				widthFilterBlock = var_widthFilterMID;
				
				marginLeftFilter = 140 + ((widthBlock - var_widthMID) / 1);
					marginLeftFilter = Math.floor(marginLeftFilter);
					
				paddingRightSlide = var_paddingRightSlideMID;
				paddingLeftTel = var_paddingLeftTelefonMID;
				paddingLeftLogo = var_paddingLeftMID;
			}
			else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN) {
				widthMainBlock = widthBlock;
				
				marginLeftMain = 125 + ((widthBlock - var_widthMIN) / (var_valueMIN / (195 - 125)));
					marginLeftMain = Math.floor(marginLeftMain);
					
				marginLeftFilter = 125 + ((widthBlock - var_widthMIN) / (var_valueMIN / (140 - 125)));
					marginLeftFilter = Math.floor(marginLeftFilter);
					
				widthFilterBlock = 220+ ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthFilterMID - 220)));
					widthFilterBlock = Math.floor(widthFilterBlock);
					
				paddingRightSlide = var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN)) );
					paddingRightSlide = Math.floor(paddingRightSlide);
					
				paddingLeftTel = var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN)));
					paddingLeftTel = Math.floor(paddingLeftTel);
					
				paddingLeftLogo = var_paddingLeftMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftMID - var_paddingLeftMIN)) );
					paddingLeftLogo = Math.floor(paddingLeftLogo);
			}
			
			var_mainBlock.css('width', widthMainBlock+'px').css('margin-left','-'+marginLeftMain+'px');
			var_filterBlock.css('width', widthFilterBlock+'px').css('margin-left','-'+marginLeftFilter+'px');
		}
		
		var_logoBlock.css('left', paddingLeftLogo+'px');
		var_aboutBlock.css('left', (122 + paddingLeftLogo)+'px');
		var_slideBlock.css('right', paddingRightSlide +'px');
		var_leftBlock.find('.left-content').css('margin-left', paddingLeftLogo+'px');
		var_telefonBlock.css('left', paddingLeftTel+'px');
		
		if (widthMainBlock < 750) {
			var var_widthTicket = (widthMainBlock - 55) - var_widthMainBlockMIN;
			
			if (var_widthTicket <= 120) {
				var_ticketsItems.find('.ticket-items').addClass('small');
			}
			else {
				var_ticketsItems.find('.ticket-items').removeClass('small');
			}
			
			var var_allWidthContent = var_widthMainBlockMIN + var_widthTicket;
			var_ticketsItems.css('width', var_allWidthContent+'px');
			var mathWidthRicket = Math.floor(var_widthTicket * 0.393);
			var_recomendedItems.find('.recommended-ticket').css('width', (253 + mathWidthRicket)+'px');
			var_recomendedItems.find('.ticket-items').addClass('small');
			var_recomendedItems.css('width', var_allWidthContent+'px');
			var_recomendedItems.find('h1').find('span').hide();
		}
		else {
			var_ticketsItems.find('.ticket-items').removeClass('small');
			var_ticketsItems.css('width', var_widthMainBlockMAX+'px');			
			var_recomendedItems.find('.ticket-items').removeClass('small');			
			var_recomendedItems.find('.recommended-ticket').css('width', '318px');
			var_recomendedItems.css('width', var_widthMainBlockMAX+'px');
			var_recomendedItems.find('h1').find('span').show();
		}
		
		if(var_hotelItems.length > 0 && var_hotelItems.is(':visible')) {
			if (var_ticketsItems.width() < 650) {
				var_hotelItems.addClass('small');
			}
			else {
				var_hotelItems.removeClass('small');
			}
		}
		
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
	 	console.log(var_oneHeight+ ' ' + var_twoHeight);
	 	if ((var_oneHeight - 19)!= var_twoHeight) {			
			var var_recomendedItems = var_recomendedContent.find('.recommended-ticket .ticket-items');
			var heightOneTicket = var_recomendedContent.find('.recommended-ticket')[0].clientHeight;
				heightOneTicket += 2;
				var_recomendedItems.css('height', heightOneTicket +'px');
				var_recomendedContent.find('.prices-of-3days .ticket').css('height', (heightOneTicket - 19) +'px');
			var heightTwoTicket = $('.recomended-content').find('.prices-of-3days')[0].clientHeight;
				heightTwoTicket = ((heightOneTicket - 35) - 17) / 2;
				heightTwoTicket = Math.floor(heightTwoTicket);
				var_recomendedContent.find('.prices-of-3days .ticket .schedule-of-prices').css('height', heightTwoTicket +'px');
				var_recomendedContent.find('.prices-of-3days .ticket .schedule-of-prices li').css('height', heightTwoTicket +'px');
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
	var var_this = $('.recomended-content').find('.prices-of-3days');
		
	var var_widthChange = $('.recomended-content').find('.recommended-ticket').width();
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


function ResizeFun() {
	ResizeCenterBlock();
	loginResize();
	//smallTicketHeight();
	$(window).resize(ResizeCenterBlock);
	$(window).resize(loginResize);
}
$(window).load(AlphaBackground);
$(window).load(ResizeFun);

$(function() {
	$(window).load(function() {
		setTimeout(smallTicketHeight, 100);
	});
});