function slideToursSlide() {
	var var_slideToursBody = $('.slideTours');
	var var_lengthTours;
	var var_allWidth = $('.slideTours .center').width();
	console.log(var_allWidth);
	var var_widthTours;
	if (var_allWidth >= 1390) {
		var_lengthTours = 6;
		var_widthTours = Math.floor(((var_allWidth / var_lengthTours) - 2));
	}
	else if (var_allWidth < 1390 && var_allWidth > 1290) {
		var_lengthTours = 5;
		var_widthTours = Math.floor(((var_allWidth / var_lengthTours) - 2));
	}
	else if (var_allWidth < 1290 && var_allWidth > 1000) {
		var_lengthTours = 4;
		var_widthTours = Math.floor(((var_allWidth / var_lengthTours) - 2));
	}
	else if (var_allWidth <= 1000) {
		var_widthTours = 248;
	}
	var_slideToursBody.find('.toursTicketsMain').css('width',var_widthTours+'px');
	
}
$(window).load(slideToursSlide);
$(window).resize(slideToursSlide);

var activeMaps = 0;
var speedAnimateChangePic = 500;
function triangleFun() {
	var startCount = 0;
    if(activeMaps == 1) {
        closeEventsMaps();
    }
    if ((activeMaps == 0) && (startCount==0)) {
        startCount = 1;
        var activeVAR = $('.slideTours').find('.active').index();
    }
}

$(window).load(triangleFun);


function CenterIMGResize() {
	var HeightAllWindow = $(window).height();
	HeightAllWindow = HeightAllWindow - 38 - 214;
	$('.innerBlockMain').css('height', HeightAllWindow+'px');
	var pathIMG = $('.innerBlockMain .IMGmain');
	var marginPathLeft = 0;
	var var_allWidth = $('.slideTours .center').width();
	if (var_allWidth >= 1390) {
		marginPathLeft = (1390 - var_allWidth) / 2;
	}
	else if (var_allWidth < 1390 && var_allWidth > 1290) {
		marginPathLeft = (1390 - var_allWidth) / 2;
		
	}
	else if (var_allWidth < 1290 && var_allWidth > 1000) {
		marginPathLeft = (1390 - var_allWidth) / 2;
	}
	else if (var_allWidth <= 1000) {
		marginPathLeft = (1390 - var_allWidth) / 2;
	}
	pathIMG.css('left', '-'+marginPathLeft+'px');
	
	if (HeightAllWindow >= 745) {
		marginPathTop = (745 - HeightAllWindow) / 2;
	}
	else if (HeightAllWindow < 745) {
		marginPathTop = (745 - HeightAllWindow) / 2;
	}
	pathIMG.css('top', '-'+marginPathTop+'px');
}

$(window).load(CenterIMGResize);

function closeEventsPhoto() {
	$('.slideTours').find('.active').find('.triangle').animate({'top' : '0px'}, 200);
	$('.toursTicketsMain').removeClass('active');
	$('.mapsBigAll').css('opacity','0');
	$('.toursBigAll').animate({opacity : 0}, 700, function() { $(this).css('display','none')});
	$('.mapsBigAll').show();
	$('.mapsBigAll').animate({opacity : 1}, 700);
	activeMaps = 1;
}
function closeEventsMaps() {
	$('.toursBigAll').css('opacity','0');
	$('.mapsBigAll').animate({opacity : 0}, 700, function() { $(this).css('display','none')});
	$('.toursBigAll').show();
	$('.toursBigAll').animate({opacity : 1}, 700);
	activeMaps = 0;
}

$(function() {
	
});