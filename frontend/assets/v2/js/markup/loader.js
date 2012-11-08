var arr_textLoad = ['Это займет от 5 до 30 секунд', 'Мы ищем среди 450 авиакомпаний','Уже почти готово', 'Ещё секундочку', 'Немного терпения']
var pointsInterval, textInterval;
function loaderChange(toStart) {
    if (toStart)
    {
        pointsInterval = setInterval(runPoints, 500);
        textInterval = setInterval(runText, 12000);
    }
    else
    {
        clearInterval(pointsInterval);
        clearInterval(textInterval);
    }
}

function runPoints() {
    var loadLight = $('#loadLight');
    var loadText = $('#changeText');

    var ind = loadLight.find('li.active').index();
    ind += 1;
    if (ind == loadLight.find('li').length) {
        ind = 0;
    }
    loadLight.find('li').removeClass('active');
    loadLight.find('li').eq(ind).addClass('active');
}

function runText() {
    var loadLight = $('#loadLight');
    var loadText = $('#changeText');

    var var_loadTextCounts = 0;
    loadText.text(arr_textLoad[var_loadTextCounts]);
    var_loadTextCounts += 1;
    if (var_loadTextCounts == arr_textLoad.length) {
        var_loadTextCounts = 0;
    }
    loadText.text(arr_textLoad[var_loadTextCounts]);
}
