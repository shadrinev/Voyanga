function SizeBox(id) {
	var popup = $('#' + id);
	if ($('#' + id).length > 0 && $('#' + id).is(':visible')) {
		$('body').css('overflow','hidden');
	}
}

function ResizeBox(id) {
	var popup = $('#'+id);
	var layer = popup.find('#layer');
	
	var winWidth = $(window).width();
	var winHeight = $(window).height()
	var pvCont = popup.find('.pv_cont');
	var pvContHeight = pvCont.height();
	var paddingTopPopUp = (winHeight - pvContHeight) / 2;
	if (paddingTopPopUp <= 10) {
		paddingTopPopUp = 10;
	}	
	popup.css('width', winWidth+'px');
	layer.css('width', (winWidth-16)+'px');
	pvCont.css('padding-top', paddingTopPopUp+'px');
	
	$(window).resize(function() {
		var winWidth = $(window).width();
		var winHeight = $(window).height()
		var pvCont = popup.find('.pv_cont');
		var pvContHeight = pvCont.height();
		var paddingTopPopUp = (winHeight - pvContHeight) / 2;
		if (paddingTopPopUp <= 10) {
			paddingTopPopUp = 10;
		}	
		popup.css('width', winWidth+'px');
		layer.css('width', (winWidth-16)+'px');
		pvCont.css('padding-top', paddingTopPopUp+'px');
	});
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
