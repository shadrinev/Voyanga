var boxPhoto = '<div id="body-popup"><div id="popupPhoto"><div id="photoBox"><div id="imgContent"></div></div></div></div>';
var load = '<div id="load"><img src="images/load.gif"></div>';
var arr_slideImg = new Array();
var arr_slideTrue = new Array();
var imgSee = false;
var clickOneShort = false;

function resizePhotoWin() {
	var var_width = $('#photoBox').innerWidth();
	var var_height = 600;
	var var_allWinWidth = $(window).width();
	var var_allWinHeight = $(window).height();
	var paddingLeft = (var_allWinWidth - var_width) / 2;
	var paddingTop = (var_allWinHeight - var_height) / 2;
	if (paddingTop < 0) {
		paddingTop = 0;
	}
	 $('#photoBox').css('left', paddingLeft+'px').css('top', (paddingTop - 20)+'px');
}
function resizeLoad() {
	var var_width = $('#load').width();
	var var_height = $('#load').height();
	var var_allWinWidth = $(window).width();
	var var_allWinHeight = $(window).height();
	var paddingLeft = (var_allWinWidth - var_width) / 2;
	var paddingTop = (var_allWinHeight - var_height) / 2;
	if (paddingTop < 0) {
		paddingTop = 0;
	}
	 $('#load').css('left', paddingLeft+'px').css('top', (paddingTop - 20)+'px');
}
function resizeImg() {
	var var_allWinHeight = $(window).height();
	var var_heightIMG = $('#photoBox img').height();
}
function creatLoad() {
	$('#popupPhoto').prepend(load);
	resizeLoad()
}
function ClosePhoto() {
	$('#popupOverlayPhoto').remove();	
	$('#body-popup').remove();
}
function createPhotoBox(obj) {
	var oneTrue = 0;
	$('body').prepend('<div id="popupOverlayPhoto"></div>');	
	$('body').prepend(boxPhoto);
	creatLoad();
	var var_this = $(obj);
	var var_href = var_this.attr('href');
	var var_len = var_this.parent().parent().find('li').length;
	var var_li = var_this.parent().parent().find('li');
	var_li.each(function(index) {
		var var_attrHref = $(this).find('a').attr('href');
		if (var_attrHref == var_href && oneTrue == 0) {
			imgSee = true;
			oneTrue += 1;
		}
		else {
			imgSee = false;
		}
		arr_slideImg[index] = var_attrHref;
		arr_slideTrue[index] = imgSee;
	});
	
	if (arr_slideImg.length > 0) {
		$('#photoBox').prepend('<div class="left"></div><div class="right"></div>');
	}
	
	$('#imgContent').append('<img src="'+var_href+'" style="opacity:0">');
	$('#imgContent').find('img').load(function() {
		$(this).show();
		if ($(this).width() > 850) {
			$(this).css('width', '850px');
		}
		else {
			$(this).css('width', 'auto');
		}
		$(this).animate({opacity : 1}, 1000);
		removeLoad();
		resizeImg();
	});
	
	clickRight(arr_slideImg, arr_slideTrue);
	clickLeft(arr_slideImg, arr_slideTrue);
	
	resizePhotoWin();
	
	$('#popupOverlayPhoto').click(function() {
		ClosePhoto();	
	});
	$('#boxClose').click(function() {
		ClosePhoto();	
	});
	$(window).resize(resizePhotoWin);
	$(window).resize(resizeImg);
}
function removeLoad() {
	$('#popupPhoto').find('#load').animate({opacity : 0}, 500, function() {
		$(this).remove();
	});
}
function clickRight(arr_slideImg, arr_slideTrue) {
	var count;
	$('#photoBox').find('.right').click(function(e) {
		e.stopPropagation();
		e.preventDefault();
		if (! clickOneShort) {
			clickOneShort = true;
			count = (arr_slideTrue.indexOf(true) + 1);
			if (count > (arr_slideTrue.length - 1)) {
				arr_slideTrue[count-1] = false;
				count = 0;
			}
			creatLoad();
			$('#photoBox').find('img').hide();
			$('#photoBox').find('img').animate({opacity : 0}, 200, function() { 
				$(this).css('width', 'auto');
				$('#photoBox img').remove();
				$('#imgContent').append('<img src="'+arr_slideImg[count]+'" style="opacity:0">');
				$('#photoBox').find('img').on('load',function() {
					$(this).show();
					if ($(this).width() > 850) {
						$(this).css('width', '850px');
					}
					else {
						$(this).css('width', 'auto');
					}
					$(this).animate({opacity : 1}, 300);
					removeLoad();
					resizeImg();
					setTimeout(function() { clickOneShort = false; }, 300);
				});
			});
			arr_slideTrue[count-1] = false;
			arr_slideTrue[count] = true;
		}
	});
}
function clickLeft(arr_slideImg, arr_slideTrue) {
	var count;
	$('#photoBox').find('.left').click(function(e) {
		e.stopPropagation();
		e.preventDefault();
		if (! clickOneShort) {
			clickOneShort = true;
			count = (arr_slideTrue.indexOf(true) - 1);
			if (count < 0) {
				arr_slideTrue[0] = false;
				count = arr_slideTrue.length - 1;
			}
			creatLoad();
			$('#photoBox').find('img').hide();
			$('#photoBox').find('img').animate({opacity : 0}, 200, function() { 
				$(this).css('width', 'auto');
				$('#photoBox img').remove();
				$('#imgContent').append('<img src="'+arr_slideImg[count]+'" style="opacity:0">');
				$('#photoBox').find('img').load(function() {
					$(this).show();
					if ($(this).width() > 850) {
						$(this).css('width', '850px');
					}
					else {
						$(this).css('width', 'auto');
					}
					$(this).animate({opacity : 1}, 300);
					removeLoad();
					resizeImg();
					setTimeout(function() { clickOneShort = false; }, 300);
				});
			});
			
			if (count == (arr_slideTrue.length - 1)) {
				arr_slideTrue[0] = false;
			}
			else {
				arr_slideTrue[count+1] = false;
			}
			arr_slideTrue[count] = true;
		}
	});
}

$(document).ready(function() {
	
	$('a.photo').click(function(e) {
		e.preventDefault();
		createPhotoBox(this); 
	});
	
	
	
	$(window).keyup(function(e) {
  			if (e.keyCode == 27) {
				ClosePhoto();
			}
	});
});

$(window).resize(function() {
	ResizeBox();
});
	