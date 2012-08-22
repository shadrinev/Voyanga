var box = '<div id="body-popup"><div id="popup"><div><div id="boxTopLeft"></div><div id="boxTopCenter"></div><div id="boxTopRight"></div></div><div><div id="boxMiddleLeft"></div><div id="boxContent"><div id="boxClose"></div></div><div id="boxMiddleRight"></div></div><div><div id="boxBottomLeft"></div><div id="boxBottomCenter"></div><div id="boxBottomRight"></div></div></div></div>';



function SizeBox() {
	var popup = $('#popup');
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

function ResizeBox() {
	var popup = $('#popup');
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

function Close() {
	$('#popupOverlay').remove();	
	$('#popup').remove();
}

function CreateBox(textCode) {
	$('body').prepend('<div id="popupOverlay"></div>');	
	$('body').prepend(box);
	$('#popup').find('#boxContent').prepend('<div id="contentBox"></div>');
	$('#popup').find('#boxContent').find('#contentBox').html(textCode);
	
	SizeBox();
	ResizeBox();
	
	$('#popupOverlay').click(function() {
		Close();	
	});
	$('#boxClose').click(function() {
		Close();	
	});
}

function LinkDone() {
	$('.hotel-details .place-buy .tmblr li a').click(function(e) {
				e.preventDefault();
				if (! $(this).hasClass('active')) {
					var var_nameBlock = $(this).attr('href');
					var_nameBlock = var_nameBlock.slice(1);
					$('.place-buy .tmblr li').removeClass('active');
					$(this).parent().addClass('active');
					$('.tab').hide();
					$('#'+var_nameBlock).show();
				}
				SizeBox();
				ResizeBox();
			});
}


$(document).ready(function() {
	var text = $('#tuda-obratno').html();
	var text2 = $('#tuda').html();
	var text3 = $('#tuda-wait').html();
	var text4 = $('#popup').html();
	
	$('a.tuda-obratno').click(function(e) {
		e.preventDefault();
		CreateBox(text); 
	});
	
	$('a.tuda').click(function(e) {
		e.preventDefault();
		CreateBox(text2); 
	});
	$('a.tuda-wait').click(function(e) {
		e.preventDefault();
		CreateBox(text3); 
	});
	
	$('a#popuphotel').click(function(e) {
		e.preventDefault();
		CreateBox(text4); 
			LinkDone();			
	});
	$('a.in-the-map').click(function(e) {
		e.preventDefault();
		CreateBox(text4);
		$('.tab').eq(0).hide();
		$('.tab').eq(1).show();
		$('.place-buy .tmblr li').removeClass('active');
		$('.place-buy .tmblr li').eq(1).addClass('active');
		SizeBox();
		ResizeBox(); 
			LinkDone();			
	});

	
	$(window).keyup(function(e) {
  			if (e.keyCode == 27) {
				Close();
			}
	});
});

$(window).resize(function() {
	ResizeBox();
});
	