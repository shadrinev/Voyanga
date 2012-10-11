function SizeBox(id) {
	$('body').css('overflow','hidden');
	var popup = $('#' + id);
	
	
	/*

	var boxContent = popup.find('#boxContent');
	var boxHeight = boxContent.innerHeight();
	var boxWidth = boxContent.innerWidth();
	var boxPopUpWidth = popup.innerWidth();
	var boxPopUpHeight = popup.innerHeight();
	var heightWindowAll = $('.popupBody').innerHeight() + $('.wrapper').scrollTop();
	//popup.css('height', (heightWindowAll)+'px');
	popup.find('#boxMiddleLeft').css('height', boxHeight+'px');
	popup.find('#boxMiddleRight').css('height', boxHeight+'px');
	popup.find('#boxTopCenter').css('width', boxWidth+'px');
	popup.find('#boxBottomCenter').css('width', boxWidth+'px');
*/
}

function ResizeBox(id) {
	var popup = $('#'+id);
	var layer = popup.find('#layer');
	
	var winWidth = $(window).width();
	var winHeight = $(window).height()
	
	popup.css('width', winWidth+'px');
	layer.css('width', (winWidth-16)+'px');
	/*

	var boxContent = popup.find('.popupBody');
	var boxPopUpWidth = boxContent.innerWidth();
	var boxPopUpHeight = boxContent.innerHeight();
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var scrollTopMine = $('.wrapper').scrollTop();
	var paddingLeft = (windowWidth - boxPopUpWidth) / 2;
	var paddingTop = (windowHeight - boxPopUpHeight) / 2;
	if (paddingTop < 0) {
		paddingTop = 0;
	}
	boxContent.css('left', paddingLeft+'px').css('top', (paddingTop)+'px');
	$('.wrapper').die('scroll',function() { });
*/
}
function btnClosePopUp() {
	$('body').css('overflow','auto');
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
