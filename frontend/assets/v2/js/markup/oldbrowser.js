var urlToOldBrowserPage = '/old/index.html';

if ($.browser.name == 'msie')
    if ($.browser.versionNumber < 8)
        window.location = urlToOldBrowserPage;

function isImageOk(img) {
    if (!img.complete) {
        return false;
    }

    if (typeof img.naturalWidth != "undefined" && img.naturalWidth == 0) {
        return false;
    }

    return true;
}

$(function() {
    setTimeout(function(){
        badImages = 0;
        for (var i = 0; i < document.images.length; i++) {
            if (!isImageOk(document.images[i])) {
                badImages++;
            }
        }
        if (badImages == document.images.length)
            $('.noimage').show();
        else
            $('.noimage').hide();
    }, 2000);
})
