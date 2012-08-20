$(function() {
	$('.order-hide').click(function(e){ 
		e.preventDefault();
		$('.recomended-content').slideUp();
		$('.minimize-rcomended .btn-minimizeRecomended').animate({top : '0px'}, 500);
	});
	$('.minimize-rcomended .btn-minimizeRecomended').click(function() {
		$('.recomended-content').slideDown();
		$(this).animate({top : '-19px'}, 500);
		$(window).load(inTheTwoLines);
		smallCityName();
		otherTimeSlide();
		widthHowLong();
		setTimeout(smallTicketHeight, 100);
	});
	$('.order-show').click(function() {
		$('.recomended-content').slideDown();
		$('.minimize-rcomended .btn-minimizeRecomended').animate({top : '-19px'}, 500);
		$(window).load(inTheTwoLines);
		smallCityName();
		otherTimeSlide();
		widthHowLong();
		setTimeout(smallTicketHeight, 100);
	});
});