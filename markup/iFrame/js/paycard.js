var browser = navigator.appName;
function placeholderIE() {
	if (browser == "Microsoft Internet Explorer")
		{
		  $(".month").val('ММ').addClass('grey');
		  $(".year").val('ГГ').addClass('grey');
		}
}
jQuery(function($){
   $(".ch01").mask("9999",{placeholder:"", completed:function(){ $('.ch02').focus();}});
   $(".ch02").mask("9999",{placeholder:"", completed:function(){ $('.ch03').focus();}});
   $(".ch03").mask("9999",{placeholder:"", completed:function(){ $('.ch04').focus();}});
   $(".ch04").mask("9999",{placeholder:"", completed:function(){ $('.firstLastName').focus();}});
   $(".firstLastName").syncTranslit({destination: 'slug'});
   $(".firstLastName").keypress(function(e) {if (e.which >= 48 && e.which <=57) {return false;}});
   $(".CVV").mask("999",{placeholder:"", completed:function(){$('.nameBank').focus();}});
	var var_countMonth = 0;
	if (browser != "Microsoft Internet Explorer")
	{
	$('.month').keypress(function(e) {
		if (var_countMonth == 0) {
			if (e.which == 48) {
				var_countMonth = 2;
			}
			else if (e.which == 49) {
				var_countMonth = 1;
			}
			else {
				return false;
			}
		}
		else if (var_countMonth == 1) {
			if (e.which == 49 || e.which == 50 || e.which == 48) {
				$('.year').focus();
			}
			else if (e.which == 8) {
				var_countMonth = 0;
			}
			else {
				return false;
			}
		}
		else if (var_countMonth == 2) {
			if (e.which == 8) {
				var_countMonth = 0;
			}
			else if (e.which > 48 && e.which < 58) {
				$('.year').focus();
			}
			else {
				return false;
			}
		}
	});
	}
	else {
	$('.month').keypress(function(e) {
		if (var_countMonth == 0) {
			if (e.which == 48) {
				var_countMonth = 2;
			}
			else if (e.which == 49) {
				var_countMonth = 1;
			}
			else {
				return false;
			}
		}
		else if (var_countMonth == 1) {
			if (e.which == 49 || e.which == 50 || e.which == 48) {
				if ($(this).val().length > 1) { 
					$('.year').focus();
				}
			}
			else if (e.which == 8) {
				var_countMonth = 0;
			}
			else {
				return false;
			}
		}
		else if (var_countMonth == 2) {
			if (e.which == 8) {
				var_countMonth = 0;
			}
			else if (e.which > 48 && e.which < 58) {
				if ($(this).val().length > 1) { 
					$('.year').focus();
				}
			}
			else {
				return false;
			}
		}
	});	
	}
   	$(".nameBank").syncTranslit({destination: 'slug2'});
   
   	$(".month").focus(function() {
		$(this).attr('placeholder','');
		var_countMonth = 0;
		$(this).removeClass('grey');
		$(this).attr('rel', $(this).val());
		$(this).val('');
	});
	$(".month").click(function() {
		var_countMonth = 0;
	});
	$(".month").blur(function() {
		if ($(this).val() == '' || $(this).val() == ' ') {
			$(this).attr('placeholder','ММ');
			var mind = $(this).attr('rel');
			if (mind.length > 0) {
				$(this).val($(this).attr('rel'));
			}
			else {
				placeholderIE();	
			}		
		}
	});
	$(".year").keyup(function() {
		if ($(this).val().length > 1) {
			$('.CVV').focus();
		}
	});
	$(".year").focus(function() {
		$(this).attr('placeholder','');
		$(this).removeClass('grey');
		$(this).attr('rel', $(this).val());
		$(this).val('');
		
	});
	$(".year").blur(function() {
		if ($(this).val() == '' || $(this).val() == ' ') {
			$(this).attr('placeholder','ГГ');
			var mind = $(this).attr('rel');
			if (mind.length > 0) {
				$(this).val($(this).attr('rel'));
			}
			else {
				placeholderIE();	
			}
		}
		var sliceMind = $(this).val();
		var slice = sliceMind.slice(0,2);
		$(this).val(slice);
	});
	
	$('.btn-order').click(function() {
		$(".payCardDiv").find('input').each(function() {
			if ($(this).val() == '' || $(this).val() == ' ') {
				$(this).addClass('error');
			}
		});
	});
	$(".payCardDiv").find('input').focus(function() {
		$(this).removeClass('error');
	});
	
	placeholderIE();
});