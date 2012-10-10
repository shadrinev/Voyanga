function resizePhotoWin() {
    var src = $('#imgContent img').attr('src');
    var img = new Image;

 
    $(img).bind('load error',function() {
        //console.log('kk');
    	var var_height = $('#imgContent').height();
    	var var_width = $('#imgContent img').innerWidth();
	    var var_allWinWidth = $(window).width();
	    var var_allWinHeight = $(window).height();
	    var paddingLeft = (var_allWinWidth - var_width) / 2;
	    var paddingTop = ((var_allWinHeight - (var_height + 84)) - 78) / 2;
	    if (paddingTop < 0) {
		paddingTop = 0;
	    }
	    $('#imgContent').css('margin-top', paddingTop+'px');
	    $('.countAndClose').css('width', var_width+'px')
    });
    img.src = src;
}

function resizeLoad() {
    var var_width = $('#hotel-img-load').width();
    var var_height = $('#hotel-img-load').height();
    var var_allWinWidth = $(window).width();
    var var_allWinHeight = $(window).height();
    var paddingLeft = (var_allWinWidth - var_width) / 2;
    var paddingTop = (var_allWinHeight - var_height) / 2;
    if (paddingTop < 0) {
	paddingTop = 0;
    }
    $('#hotel-img-load').css('left', paddingLeft+'px').css('top', (paddingTop - 20)+'px');
}

$(window).resize(function() {
	resizePhotoWin();
    resizeLoad();
});