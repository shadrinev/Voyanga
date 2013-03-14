var urlToOldBrowserPage = '/old/index.html';

if ($.browser.name == 'msie')
    if ($.browser.versionNumber < 8)
        window.location = urlToOldBrowserPage;