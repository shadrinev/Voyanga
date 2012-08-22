function WidthMine() {
	var activeLI = $('.slide-turn-mode').find('.active');
	var switchSlide = $('.slide-turn-mode').find('.switch');
	var widthLI = activeLI.innerWidth();
	var pos = activeLI.position();		
	var paddingLeft = (pos.left - 13);
	var centerWidth = widthLI;
	
	switchSlide.css('width', (widthLI + 30)+'px').css('left',paddingLeft+'px');
	switchSlide.find('.c').css('width', centerWidth +'px');
	
	$('.btn').click(function() {
		if (! $(this).hasClass('active')) {
			activeLI = $(this);
			widthLI = activeLI.innerWidth();
			pos = activeLI.position();		
			paddingLeft = (pos.left - 13);
			centerWidth = widthLI;
			$('.btn.active').find('a').animate({opacity : 0.5},300, function() {
				$('.btn').removeClass('active');
				$(this).animate({opacity : 1},500);
			});
			switchSlide.animate({width : (widthLI +30) +'px', left : paddingLeft +'px'}, 400, function() {
						activeLI.addClass('active');
			});
			switchSlide.find('.c').animate({width : centerWidth+'px'}, 400);
		}
	});
}
$(window).load(WidthMine);