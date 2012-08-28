var var_man = '<div class="man"></div>';
var var_child = '<div class="child"></div>';
function inputMan() {
	if ($('.how-many-man .popup').length > 0) {
		var var_allVal = 0;
		var var_input = $('.how-many-man .popup').find('input');
		var_input.each(function() {
			if ($(this).val() == '') {
				var_allVal	+= 0;
			}
			else {
				var_allVal += Math.abs(parseInt($(this).val()));
			}
		});		
		$('.how-many-man').find('.content').remove();
		$('.how-many-man').append('<div class="content"></div>');
		var childVal = 0;
		if (var_allVal > 5) {
			var_input.eq(0).each(function() {
				if ($(this).val() > 1) {
					$(this).addClass('active');
					if ($(this).parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_man);
						$('.how-many-man').find('.content').append('<div class="count"><span>x</span>'+$(this).val()+'</div>');
					}					
				}
				else if ($(this).val() == 1){
					$(this).addClass('active');
					if ($(this).parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_man);
					}
					
				}
				else if ($(this).val() == '' || $(this).val() == 0) {
					if ($(this).parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_man);
					}
				}
				else {
					$(this).removeClass('active');
				}
			});
			
			if (var_input.eq(1).val() == '') {
				childVal += 0;
			}
			else {
				childVal += Math.abs(parseInt(var_input.eq(1).val()));
			}
			if (var_input.eq(2).val() == '') {
				childVal += 0;
			}
			else {
				childVal += Math.abs(parseInt(var_input.eq(2).val()));
			}
			if (childVal > 1) {
				$('.how-many-man').find('.content').append(var_child);
				$('.how-many-man').find('.content').append('<div class="count"><span>x</span>'+childVal+'</div>');
			}
			else if (childVal == 1) {
				$('.how-many-man').find('.content').append(var_child);
			}
			var_input.each(function() {
				if ($(this).val() > 0) {
					$(this).addClass('active');
				}
				else {
					$(this).removeClass('active');
				}
			});
		}
		else {
			var_input.each(function() {
				if ($(this).val() > 0) {
					$(this).addClass('active');
					if ($(this).parent().hasClass('adults')) {
						for (i = 0; i < $(this).val(); i++) {
							$('.how-many-man').find('.content').append(var_man);
						}
					}
					else if ($(this).parent().hasClass('childs') || $(this).parent().hasClass('small-childs')) {
						for (i = 0; i < $(this).val(); i++) {
							$('.how-many-man').find('.content').append(var_child);
						}
					}
				}
				else if ($(this).val() == '' || $(this).val() == 0) {
					if ($(this).parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_man);
					}
				}
				else {
					$(this).removeClass('active');
				}
			});
		}
	}
}
function emptyInput() {
	var var_input = $('.how-many-man .popup').find('input');
	if (var_input.eq(0).val() == '' || var_input.eq(0).val() == 0) {
		var_input.eq(0).val(1);
	}
	if (var_input.eq(1).val() == '' || var_input.eq(1).val() == 0) {
		var_input.eq(1).val(0);
	}
	if (var_input.eq(2).val() == '' || var_input.eq(2).val() == 0) {
		var_input.eq(2).val(0);
	}
}
function inputSelectMan() {
	inputMan();
	var var_input = $('.how-many-man .popup').find('input');
	var_input.blur(function() {
		if ($(this).val() > 0) {
			$(this).addClass('active');
		}
		else if ($(this).val() == '' && $(this).attr('rel') > 0) {
			$(this).val($(this).attr('rel'));
			$(this).addClass('active');
		}
		else {
			$(this).removeClass('active');
			$(this).val(0);
		}
	});
	var_input.focus(function() {
		$(this).addClass('active');
		$(this).attr('rel', $(this).val());
		$(this).val('');
	});
	var_input.change(function() {
		emptyInput();
		inputMan();
	});
	var_input.keyup(function(e) {
		if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57))
	    {
	        $(this).val('');
	        return false;
	    } 
	    else {
		  inputMan();
		  //countAll_x();  
	    }   
		
	})
	
}
function countAll_x() {
	var var_input = $('.how-many-man .popup').find('input');
	var var_allVal;
	var_input.each(function() {
		if ($(this).val() == '') {
			var_allVal	+= 0;
		}
		else {
			var_allVal += Math.abs(parseInt($(this).val()));
		}
	});	
	console.log(var_allVal);
	if (var_allVal >= 9) {
		var var_x = 9 - Math.abs(parseInt(var_input.eq(1).val())) - Math.abs(parseInt(var_input.eq(2).val()));
		var var_y = 9 - Math.abs(parseInt(var_input.eq(0).val())) - Math.abs(parseInt(var_input.eq(2).val()));
		if (var_input.eq(0).val() > var_x) {
			var_input.eq(0).val(var_x);
		}
	}
}
function closeHowMany() {
	$('.how-many-man').find('.popup').removeClass('active');
	$('.how-many-man .btn').removeClass('active');
}
$(window).load(inputSelectMan);
$(function() {
	if ($('.tumblr input').attr('checked') == 'checked') {
		$('.tumblr .switch').css('left', '35px');
		$('.tumblr .two').addClass('active');
	}
	else {
		$('.tumblr .one').addClass('active');
	}
	$('.tumblr .two').click(function(e) {
	e.preventDefault();
	e.stopPropagation();
		if (! $(this).hasClass('active')) {
			$('.tumblr div').removeClass('active');
			$('.tumblr .switch').animate({'left': '35px'}, 200)
			$(this).addClass('active');
			$('.tumblr input').attr('checked','checked');
		}
	});
	$('.tumblr .one').click(function(e) {
	e.preventDefault();
	e.stopPropagation();
		if (! $(this).hasClass('active')) {
			$('.tumblr div').removeClass('active');
			$('.tumblr .switch').animate({'left': '-1px'}, 200)
			$(this).addClass('active');
			$('.tumblr input').removeAttr('checked');
		}
	});
	$('.how-many-man .btn').click(function() {
		if(! $(this).hasClass('active')) {
			$('.how-many-man').find('.popup').addClass('active');
			$(this).addClass('active');
		}
		else {
			closeHowMany();
		}
	});
	var mouse_is_inside = false;
	$('.how-many-man .popup, .how-many-man .btn').hover(function(){ 
			mouse_is_inside=true;
		},function(){ 
			mouse_is_inside=false;
    });
	$('body').mousedown(function(){ 		
        if(! mouse_is_inside) {
			closeHowMany();
		}
    });
	$(window).keypress(function(e) {
  			if (e.keyCode == 27) {
				closeHowMany();
			}
	});
});