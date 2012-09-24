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
	var boxContent = popup.find('.popupBody');
	var boxPopUpWidth = boxContent.innerWidth();
	var boxPopUpHeight = boxContent.innerHeight();
	console.log('flkjashahasjhJHKJHKJHKJHKJH '+boxPopUpWidth+' ADFJHASDKJAH '+boxPopUpHeight);
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var paddingLeft = (windowWidth - boxPopUpWidth) / 2;
	var paddingTop = (windowHeight - boxPopUpHeight) / 2;
	if (paddingTop < 0) {
		paddingTop = 0;
	}
	boxContent.css('left', paddingLeft+'px').css('top', (paddingTop - 20)+'px');
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
});
