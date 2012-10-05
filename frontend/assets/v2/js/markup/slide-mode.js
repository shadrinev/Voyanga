function WidthMine() {
	var var_activeLI = $('.slide-turn-mode ul').find('.active');
	var var_activeLIindex = var_activeLI.index();
	var var_switchSlide = $('.slide-turn-mode').find('.switch');
	var var_speed = 400;
	
	var arr_plannerWidth = [143, 0];
	var arr_aviaticketsWidth = [135, 155];
	var arr_hotelWidth = [95, 295];
	var arr_finishStagesWidth = [148, 403];
	
	var arr_valueWidth = [arr_plannerWidth, arr_aviaticketsWidth, arr_hotelWidth, arr_finishStagesWidth]
	
	var_switchSlide.css('width', arr_valueWidth[var_activeLIindex][0] +'px').css('left', arr_valueWidth[var_activeLIindex][1]+'px');
	var_switchSlide.find('.c').css('width', (arr_valueWidth[var_activeLIindex][0] - 27) +'px');

	$('.slide-turn-mode .btn').click(function(e) {
		e.preventDefault();
		if (! $(this).hasClass('active')) {
			$('.btn').removeClass('active');
			$(this).addClass('active');
			var_activeLI = $('.slide-turn-mode ul').find('.active');
			var_activeLIindex = var_activeLI.index();
			var_switchSlide.animate({width : arr_valueWidth[var_activeLIindex][0] +'px', left : arr_valueWidth[var_activeLIindex][1]+'px'}, var_speed , function() {
						$(this).addClass('active');
			});
			var_switchSlide.find('.c').animate({width : (arr_valueWidth[var_activeLIindex][0] - 27) +'px'}, var_speed );
		}
	});
}