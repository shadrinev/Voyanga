var arr_textLoad = ['Это займет от 5 до 30 секунд', 'Мы ищем среди 450 авиакомпаний','Уже почти готово', 'Ещё секундочку', 'Немного терпения']
function loaderChange() {
	var loadLight = $('#loadLight');
	var loadText = $('#changeText');
	
	setInterval(function() {
		var ind = loadLight.find('li.active').index();
		ind += 1;
		if (ind == loadLight.find('li').length) {
			ind = 0;
		}
		loadLight.find('li').removeClass('active');
		loadLight.find('li').eq(ind).addClass('active');
	}, 600);
	
	var var_loadTextCounts = 0;
	loadText.text(arr_textLoad[var_loadTextCounts]);
	
	setInterval(function() {
		var_loadTextCounts += 1;
		if (var_loadTextCounts == arr_textLoad.length) {
			var_loadTextCounts = 0;
		}
		loadText.text(arr_textLoad[var_loadTextCounts]);
	}, 12000)
}

$(window).load(loaderChange);