function resizePhotoWinHandler() {
    console.log('kk');
    var var_height = $('#imgContent').height();
    var var_width = $('#imgContent img').innerWidth();
    var var_allWinWidth = $(window).width();
    var var_allWinHeight = $(window).height();
    var paddingLeft = (var_allWinWidth - var_width) / 2;
    var paddingTop = ((var_allWinHeight - (var_height + 84)) - 78) / 2;
    if (paddingTop < 0) {
        paddingTop = 0;
    }
    paddingTop = Math.round(paddingTop);
    
    $('.countAndClose').css('width', var_width+'px');
        //$('#imgContent').css('margin-top', paddingTop+'px');
        //$('#titleNamePhoto').css('height', (paddingTop+50)+'px');
        //$('#photoBox').animate({'width':'100%'},10);
    //console.log('pddingtop:',paddingTop,' allwin:',var_allWinHeight,'h:',var_height);
}

function resizePhotoWin() {
    console.log('try start');
    var src = $('#body-popup-Photo img').attr('src');
    var img = new Image;
 
    //$(img).bind('load error',resizePhotoWinHandler);
    //resizePhotoWinHandler();
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
    $('#hotel-img-load').css('left', (paddingLeft-20)+'px').css('top', (paddingTop - 20)+'px');
}

$(window).resize(function() {
	//resizePhotoWin();
    resizeLoad();
});