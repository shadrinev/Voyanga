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
