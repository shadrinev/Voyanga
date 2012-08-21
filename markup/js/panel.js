function inputSelectMan() {
	var var_input = $('.how-many-man .popup').find('input');
	var_input.each(function() {
		if ($(this).val() > 0) {
			$(this).addClass('active');
		}
		else {
			$(this).removeClass('active');
		}
	});
	
	var_input.focus(function() {
		$(this).addClass('active');
	});
	var_input.blur(function() {
		if ($(this).val() > 0) {
			$(this).addClass('active');
		}
		else {
			$(this).removeClass('active');
		}
	});
}
function closeHowMany() {
	$('.how-many-man').find('.popup').removeClass('active');
	$('.how-many-man .btn').removeClass('active');
}
$(window).load(inputSelectMan);
$(function() {
	var var_timeline = $('.timeline');
	var var_timelineIsset = var_timeline.length > 0 && var_timeline.is(':visible');
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
	if (var_timelineIsset) {
		$('.condition').css('top', '68px');	
	}
	else {
		$('.condition').css('top', '0px');
		$('.btn-timeline-and-condition').hide();
	}
	$('.btn-condition').click(function() {
		if (! $(this).hasClass('active')) {
			$('.btn-timeline-and-condition a').removeClass('active');
			$(this).addClass('active');
			$('.timeline').animate({'top': '-'+$('.timeline').height()+'px'},400, function() { $('.slide-tmblr').css('overflow','visible'); }).addClass('hide');
			$('.condition').animate({'top': '0px'},400).removeClass('hide');
			
		}
	});
	$('.btn-timeline').click(function() {
		if (! $(this).hasClass('active')) {
			$('.slide-tmblr').css('overflow','hidden');
			$('.btn-timeline-and-condition a').removeClass('active');
			$(this).addClass('active');
			$('.timeline').animate({'top': '0px'},400).removeClass('hide');
			$('.condition').animate({'top': '68px'},400).addClass('hide');
		}
	});
	$('.tumblr .two').click(function() {
		$('.tumblr div').removeClass('active');
		$('.tumblr .switch').animate({'left': '35px'}, 200)
		$(this).addClass('active');
		$('.where').addClass('date');
	});
	$('.tumblr .one').click(function() {
		$('.tumblr div').removeClass('active');
		$('.tumblr .switch').animate({'left': '-1px'}, 200)
		$(this).addClass('active');
		$('.where').removeClass('date');
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