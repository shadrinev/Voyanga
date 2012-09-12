function SizeBox(id) {
	var popup = $('#' + id);
	var boxContent = popup.find('#boxContent');
	var boxHeight = boxContent.innerHeight();
	var boxWidth = boxContent.innerWidth();
	var boxPopUpWidth = popup.innerWidth();
	var boxPopUpHeight = popup.innerHeight();
	popup.css('width', (boxPopUpWidth+1)+'px').css('height', boxPopUpHeight+'px');
	popup.find('#boxMiddleLeft').css('height', boxHeight+'px');
	popup.find('#boxMiddleRight').css('height', boxHeight+'px');
	popup.find('#boxTopCenter').css('width', boxWidth+'px');
	popup.find('#boxBottomCenter').css('width', boxWidth+'px');
}

function ResizeBox(id) {
	var popup = $('#'+id);
	var boxContent = popup.find('#boxContent');
	var boxPopUpWidth = popup.innerWidth();
	var boxPopUpHeight = popup.innerHeight();
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var paddingLeft = (windowWidth - boxPopUpWidth) / 2;
	var paddingTop = (windowHeight - boxPopUpHeight) / 2;
	if (paddingTop < 0) {
		paddingTop = 0;
	}
	popup.css('left', paddingLeft+'px').css('top', (paddingTop - 20)+'px');
}



$(document).ready(function() {
    return;
	var text4 = $('#popup').html();


	$('a#popuphotel').click(function(e) {
		e.preventDefault();
		LinkDone();
		$('a.photo').click(function(e) {
			e.preventDefault();
			createPhotoBox(this);
		});
	});
	$('a.in-the-map').click(function(e) {
		e.preventDefault();
		CreateBox(text4);
		$('.tab').eq(0).hide();
		$('.tab').eq(1).show();
		$('.place-buy .tmblr li').removeClass('active');
		$('.place-buy .tmblr li').eq(1).addClass('active');
		LinkDone();
		$('a.photo').click(function(e) {
			e.preventDefault();
			createPhotoBox(this);
		});
	});



});
