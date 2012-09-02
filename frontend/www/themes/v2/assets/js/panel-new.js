var var_maxSizePeople = 9;
var var_maxAdults = 9;
var var_maxChild = 8;


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

/*
*/
$(window).load(clickHowManyBtn);
function closeHowMany() {
	$('.how-many-man').find('.popup').removeClass('active');
	$('.how-many-man .btn').removeClass('active');
}
