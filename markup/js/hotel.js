function checkUlList() {
	$('.details').each(function() {
		console.log($(this).width());
		var var_this = $(this).find('ul li');
		var var_length = var_this.length;

			for (i = 0; i < var_length; i++) {
				if (i == 0 || i == 1) {
					var_this.eq(i).addClass('not-show');	
				}
				else {
					var_this.eq(i).hide();	
				}

		}
	});
	$('.tab-ul a').click(function() {
		var var_thisLink = $(this);
		var var_this = $(this).parent().parent();
		if (! $(this).hasClass('active')) {	
			var_thisLink.text('Свернуть все рузультаты');
			var_thisLink.addClass('active');
			var_this.find('ul li[class != "not-show"]').slideDown();
		}
		else {
			var_this.find('ul li[class != "not-show"]').slideUp(300, function() {
				var_thisLink.removeClass('active');
				var_thisLink.text('Посмотреть все результаты');
			});
		}
	});
	
}
$(window).load(checkUlList);