function slideToursSlide() {
	var var_slideToursBody = $('.slideTours');
	var var_lengthTours;
	var var_allWidth = $(window).width();
	var var_widthTours;
	console.log(var_allWidth);
	if (var_allWidth > 1390) {
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
	else if (var_allWidth < 1000) {
		var_widthTours = 248;
	}
	var_slideToursBody.find('.toursTicketsMain').css('width',var_widthTours+'px');
	
}
$(window).load(slideToursSlide);
$(window).resize(slideToursSlide);


function triangleFun() {
	$('.toursTicketsMain').click(function() {
		if (! $(this).hasClass('active')) {
			$('.slideTours').find('.active').find('.triangle').animate({'top' : '0px'}, 300, function() {
				$('.slideTours').find('.active').removeClass('active');
			});
			
			$(this).find('.triangle').animate({'top' : '-18px'}, 500, function() {
				$(this).parent().addClass('active');
			});
		}
	});
}
$(window).load(triangleFun);