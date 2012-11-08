var arr_textLoad = ['Это займет от 5 до 30 секунд', 'Мы ищем среди 450 авиакомпаний','Уже почти готово', 'Ещё секундочку', 'Немного терпения'];
var pointsInterval, textInterval, started = false, var_loadTextCounts;
function loaderChange(toStart) {
    var loadText = $('#changeText');
    if (toStart)
    {
        if (started)
            return;
        var_loadTextCounts = 0;
        loadText.text(arr_textLoad[var_loadTextCounts]);
        pointsInterval = setInterval(runPoints, 500);
        textInterval = setInterval(runText, 12000);
        started = true;
    }
    else
    {
        clearInterval(pointsInterval);
        clearInterval(textInterval);
        started = false;
    }
}

function runPoints() {
    var loadLight = $('#loadLight');
    var ind = loadLight.find('li.active').index();
    ind += 1;
    if (ind == loadLight.find('li').length) {
        ind = 0;
    }
    loadLight.find('li').removeClass('active');
    loadLight.find('li').eq(ind).addClass('active');
}

function runText() {
    var loadText = $('#changeText');
    var_loadTextCounts += 1;
    if (var_loadTextCounts == arr_textLoad.length) {
        var_loadTextCounts = 0;
    }
    loadText.text(arr_textLoad[var_loadTextCounts]);
}
