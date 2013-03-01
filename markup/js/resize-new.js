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