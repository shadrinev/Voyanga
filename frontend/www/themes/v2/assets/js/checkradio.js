function replaceInput(input){
  var lenInput = input.length;
  for(var i = 0; i < lenInput; i++) {
    var classCheck = input.eq(i).attr('type');
    var htm = '';
    var checked = (input.eq(i).attr("checked")) ? " on" : "";
    htm += '<label class="ui-hover cF" for="'+input.eq(i).attr("id")+'">';
    htm += '<div class="ui-control '+classCheck+checked+'"></div>';
    htm += '<div class="fl">';
    htm += '<div class="ui-label">'+input.eq(i).data("label")+'</div>';
    htm += '</div>';
    htm += '</label>';
    input.eq(i).after(htm);
    input.eq(i).hide();
  } 
}
$(function(){
  var main = $(document);
  var checkbox = main.find('input[type="checkbox"]');
  var radio = main.find('input[type="radio"]');
  replaceInput(checkbox);
  replaceInput(radio);
  $('.ui-hover').click(function(){

    var button = $(this).prev().attr('type');
    if(button == 'radio') $('.radio').removeClass('on');	
    if($(this).find('.ui-control').hasClass('on') != true) $(this).find('.ui-control').addClass('on');
    else $(this).find('.ui-control').removeClass('on');		
  });

});
