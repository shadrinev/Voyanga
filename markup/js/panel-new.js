var var_manDIV = '<div class="man"></div>';
var var_childDIV = '<div class="child"></div>';
var var_maxSizePeople = 9;
var var_maxAdults = 9;
var var_maxChild = 8;

function inputSelectMan() {
	if ($('.how-many-man .popup').length > 0) {
		var var_allVal = 0;
		
		var var_adults;
		var var_child;
		var var_smallChild;

		var var_input = $('.how-many-man .popup').find('input');
		
		var_input.each(function() {
			if ($(this).val() == '') {
				var_allVal	+= 0;
			}
			else {
				var_allVal += Math.abs(parseInt($(this).val()));
			}
		});	
		if (var_input.eq(0).val() == '') {
			var_adults = 1;
		}
		else {
			var_adults = Math.abs(parseInt(var_input.eq(0).val()));	
		}
		if (var_input.eq(1).val() == '') {
			var_child = 0;
		}
		else {
			var_child = Math.abs(parseInt(var_input.eq(1).val()));	
		}
		if (var_input.eq(2).val() == '') {
			var_smallChild = 0;
		}
		else {
			var_smallChild = Math.abs(parseInt(var_input.eq(2).val()));	
		}
		$('.how-many-man').find('.content div').remove();
		if (var_allVal > 5) {
			var_input.eq(0).each(function() {
				if ($(this).val() > 1) {
					$(this).addClass('active');
					if ($(this).parent().parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_manDIV);
						$('.how-many-man').find('.content').append('<div class="count"><span>x</span>'+$(this).val()+'</div>');
					}					
				}
				else if ($(this).val() == 1){
					$(this).addClass('active');
					if ($(this).parent().parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_manDIV);
					}
					
				}
				else if ($(this).val() == '' || $(this).val() == 0) {
					if ($(this).parent().parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_manDIV);
					}
				}
				else {
					$(this).removeClass('active');
				}
			});
			var childVal = 0;
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
				$('.how-many-man').find('.content').append(var_childDIV);
				$('.how-many-man').find('.content').append('<div class="count"><span>x</span>'+childVal+'</div>');
			}
			else if (childVal == 1) {
				$('.how-many-man').find('.content').append(var_childDIV);
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
					if ($(this).parent().parent().hasClass('adults')) {
						for (i = 0; i < $(this).val(); i++) {
							$('.how-many-man').find('.content').append(var_manDIV);
						}
					}
					else if ($(this).parent().parent().hasClass('childs') || $(this).parent().parent().hasClass('small-childs')) {

						for (i = 0; i < $(this).val(); i++) {
							$('.how-many-man').find('.content').append(var_childDIV);
						}
					}
				}
				else if ($(this).val() == '' || $(this).val() == 0) {
					if ($(this).parent().parent().hasClass('adults')) {
						$('.how-many-man').find('.content').append(var_manDIV);
					}
				}
				else {
					$(this).removeClass('active');
				}
			});
		}

		zeroSearch();
	}
}

function clickHowManyBtn() {
	$('.how-many-man .content').click(function(e) {
		if(! $(this).hasClass('active')) {
			$(this).addClass('active');
			$('.how-many-man .btn').addClass('active');
			$('.how-many-man').find('.popup').addClass('active');
		}
		else {
			$(this).removeClass('active');
			closeHowMany();
			closeHowMany();
		}
	});
	$('.how-many-man .btn').click(function(e) {
		if(! $(this).hasClass('active')) {
			$(this).addClass('active');
			$('.how-many-man .content').addClass('active');
			$('.how-many-man').find('.popup').addClass('active');
		}
		else {
			closeHowMany();
		}
	});
	var mouse_is_inside = false;
	$('.how-many-man .popup, .how-many-man .btn, .how-many-man .content').hover(function(){ 
			mouse_is_inside=true;
		},function(){ 
			mouse_is_inside=false;
    });
	$('body').mousedown(function(){ 		
        if(! mouse_is_inside) {
			closeHowMany();
			$('.how-many-man .content').removeClass('active');
		}
    });
	$(window).keypress(function(e) {
  			if (e.keyCode == 27) {
				closeHowMany();
				$('.how-many-man .content').removeClass('active');
			}
	});
}
function getValuesPeople() {
	var var_input = $('.how-many-man .popup').find('input');
	var arr_people = new Array();
	var_input.each(function(index) {
		if (var_input.eq(index).val() == '') {
			arr_people[index] = 0;
		}
		else {
			arr_people[index] = Math.abs(parseInt(var_input.eq(index).val()));	
		}
	});
	return arr_people;
}
function changeAdultsCount() {
	var arr_peoples = getValuesPeople();
	if((arr_peoples[0]+arr_peoples[1]+arr_peoples[2]) > var_maxSizePeople){
		arr_peoples[0] = var_maxSizePeople - arr_peoples[1] - arr_peoples[2];
	}
	if(arr_peoples[0]<1){ arr_peoples[0] = '';}
	$('.how-many-man .popup').find('input').eq(0).val(arr_peoples[0]);
	changeInfantCount();
	inputSelectMan();	
}
function changeChildCount() {
	var arr_peoples = getValuesPeople();
	if((arr_peoples[0]+arr_peoples[1]+arr_peoples[2]) > var_maxSizePeople){
		arr_peoples[1] = var_maxSizePeople - arr_peoples[0] - arr_peoples[2];
	}
	if(arr_peoples[1]==0){ arr_peoples[1] = 0;}
	$('.how-many-man .popup').find('input').eq(1).val(arr_peoples[1]);
	inputSelectMan();
}
function changeInfantCount() {
	var arr_peoples = getValuesPeople();
	if((arr_peoples[0]+arr_peoples[1]+arr_peoples[2]) > var_maxSizePeople){
		arr_peoples[2] = var_maxSizePeople - arr_peoples[0] - arr_peoples[1];
	}
	if(arr_peoples[2] > arr_peoples[0]){
		arr_peoples[2] = arr_peoples[0];
	}
	if(arr_peoples[2]==0){ arr_peoples[2] = 0;}
	$('.how-many-man .popup').find('input').eq(2).val(arr_peoples[2]);
	inputSelectMan();
}
function eachFunctionPeoples(obj) {
	var var_count = $(obj).val();
	$(obj).attr('rel', var_count);
}
function initPeoplesInputs() {
	$('.how-many-man .popup').find('input').eq(0).keyup(changeAdultsCount);
	$('.how-many-man .popup').find('input').eq(1).keyup(changeChildCount);
	$('.how-many-man .popup').find('input').eq(2).keyup(changeInfantCount);
	$('.how-many-man .popup').find('input').blur(function() {
		if ($(this).val() == '') {
			$(this).val($(this).attr('rel'));
		}
	});
	$('.how-many-man .popup').find('input').focus(function(e) {
		eachFunctionPeoples(this);
		$(this).val('');
	});
	$('.how-many-man .popup').find('input').eq(1).focus(function(e) {
		if ($(this).val() == '' || $(this).val() == 0) {
			$(this).val('');
		}
		else {
			changeChildCount();
		}
	});
	$('.how-many-man .popup').find('input').eq(2).focus(function(e) {
		if ($(this).val() == '' || $(this).val() == 0) {
			$(this).val('');
		}
		else {
			changeInfantCount();
		}
	});
	$('.how-many-man .popup').find('.adults').find('.plusOne').click(function(e) {
		e.preventDefault();
		var var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
		var_valCount++;
		$(this).parent().find('input').val(var_valCount);
		changeAdultsCount();
	});
	$('.how-many-man .popup').find('.adults').find('.minusOne').click(function(e) {
		e.preventDefault();
		var var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
		var_valCount--;
		if (var_valCount < 1) {
			var_valCount = 1;
		}
		$(this).parent().find('input').val(var_valCount);
		changeAdultsCount();
	});
	
	$('.how-many-man .popup').find('.childs').find('.plusOne').click(function(e) {
		e.preventDefault();
		var var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
		var_valCount++;
		$(this).parent().find('input').val(var_valCount);
		changeChildCount();
	});
	$('.how-many-man .popup').find('.childs').find('.minusOne').click(function(e) {
		e.preventDefault();
		var var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
		var_valCount--;
		if (var_valCount <= 0) {
			var_valCount = 0;
		}
		$(this).parent().find('input').val(var_valCount);
		changeChildCount();
	});
	
	$('.how-many-man .popup').find('.small-childs').find('.plusOne').click(function(e) {
		e.preventDefault();
		var var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
		var_valCount++;
		$(this).parent().find('input').val(var_valCount);
		changeInfantCount();
	});
	$('.how-many-man .popup').find('.small-childs').find('.minusOne').click(function(e) {
		e.preventDefault();
		var var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
		var_valCount--;
		if (var_valCount <= 0) {
			var_valCount = 0;
		}
		$(this).parent().find('input').val(var_valCount);
		changeInfantCount();
	});
	
	$('.how-many-man .popup').find('.adults').find('input').hover(function() {
		$(this).parent().find('.plusOne').show();
		$(this).parent().find('.minusOne').show();
	});
	$('.how-many-man .popup').find('.adults').hover(function() { }, function() {
		$(this).parent().find('.plusOne').hide();
		$(this).parent().find('.minusOne').hide();
	});
	
	$('.how-many-man .popup').find('.childs').find('input').hover(function() {
		$(this).parent().find('.plusOne').show();
		$(this).parent().find('.minusOne').show();
	});
	$('.how-many-man .popup').find('.childs').hover(function() { }, function() {
		$(this).parent().find('.plusOne').hide();
		$(this).parent().find('.minusOne').hide();
	});
	
	$('.how-many-man .popup').find('.small-childs').find('input').hover(function() {
		$(this).parent().find('.plusOne').show();
		$(this).parent().find('.minusOne').show();
	});
	$('.how-many-man .popup').find('.small-childs').hover(function() { }, function() {
		$(this).parent().find('.plusOne').hide();
		$(this).parent().find('.minusOne').hide();
	});
	
	$('.plusOne').hover(function() {
		$(this).addClass('active');
		$('.minusOne').addClass('active')
	}, function() {
		$(this).removeClass('active');
		$('.minusOne').removeClass('active')
	});
	$('.minusOne').hover(function() {
		$(this).addClass('active');
		$('.plusOne').addClass('active')
	}, function() {
		$(this).removeClass('active');
		$('.plusOne').removeClass('active')
	});
}

function zeroSearch() {
	var var_input = $('.how-many-man .popup').find('input');
	var_input.each(function() {
		if ($(this).val() == 0) {
			$(this).removeClass('active');
		}
		else {
			$(this).addClass('active');
		}
	});
}
/*


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

*/
$(window).load(clickHowManyBtn);
function closeHowMany() {
	$('.how-many-man').find('.popup').removeClass('active');
	$('.how-many-man .btn').removeClass('active');
}

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
	initPeoplesInputs();
	inputSelectMan();
	
	
	// HIDE PANEL
	$('.btn-minimizePanel').click(function() {
		var var_SubHead = $('.sub-head');
		var var_speed =  300;
		var var_heightSubHead = var_SubHead.height();
		if(! $(this).hasClass('active')) {	
			$('.btn-minimizePanel').html('<span></span> развернуть');
			$('.btn-minimizePanel').addClass('active');	
			var_SubHead.animate({'margin-top' : '-'+(var_heightSubHead-4)+'px'}, var_speed);		
		}
		else {
			$('.btn-minimizePanel').html('<span></span> свернуть');
			$('.btn-minimizePanel').removeClass('active');
			var_SubHead.animate({'margin-top' : '0px'}, var_speed);			
		}
	});
});