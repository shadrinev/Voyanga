function SizeBox(id) {
	$('body').css('overflow','hidden');
	var popup = $('#' + id);
	$('#pv_box').find('form').focus();
	
	/*
$(document).on('keydown', function(e){
		var modalKeys = [40,38,37,39,36,35,33,34];
		if ($.inArray(e.which, modalKeys) >= 0) {
			e.preventDefault();
			$('.' + localOptions.overlayClass).trigger(e);
		}
	});

	
	$(window).on('keydown', function(e){
		if ($(e.target) != popup) {
			var modalKeys = [40,38,37,39,36,35,33,34];
			if ($.inArray(e.which, modalKeys) >= 0) {
				e.preventDefault();
				popup.trigger(e);
				console.log(e);
			}
		}
e.stopPropagation();
		return false;

	});
	

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
