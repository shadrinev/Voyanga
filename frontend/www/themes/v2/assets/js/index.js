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


function triangleFun() {
	var stratCount = 0;
	$('.toursTicketsMain').click(function() {

			if (! $(this).hasClass('active')) {
				$('.slideTours').find('.active').find('.triangle').animate({'top' : '0px'}, 300, function() {
					$('.slideTours').find('.active').removeClass('active');
				});
				
				$(this).find('.triangle').animate({'top' : '-18px'}, 500, function() {
					$(this).parent().addClass('active');
					
				});
				
				var rel = $(this).attr('rel');
				$('.IMGmain').clone().appendTo('.innerIMG');
				var varLeftPos = $('.IMGmain').eq(1).css('left');
				var varTopPos = $('.IMGmain').eq(1).css('top');
				varLeftPos = parseInt(varLeftPos.slice(0,-2));
				varTopPos = parseInt(varTopPos.slice(0,-2));
				
				var varLeftPosStart = varLeftPos;
				var varTopPosStart = varTopPos;
				$('.IMGmain').eq(1).css('opacity', '0').css('left', varLeftPosStart+'px').css('top',varTopPosStart+'px').find('img').attr('src',rel);
				
				$('.IMGmain').eq(1).find('img').load(function() {
					$('.IMGmain').eq(0).animate({opacity : 0}, 700, function() { $(this).remove(); });
					$('.IMGmain').eq(1).animate({'opacity' : 1, 'left': varLeftPos+'px', 'top':varTopPos+'px'}, 400);
				});				
			}

		
	});
}
$(window).load(triangleFun);


function CenterIMGResize() {
	var HeightAllWindow = $(window).height();
	HeightAllWindow = HeightAllWindow - 38 - 215;
	$('.innerIMG').css('height', HeightAllWindow+'px');
	var pathIMG = $('.innerIMG .IMGmain');
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
$(window).resize(CenterIMGResize);


$(function() {
	
});