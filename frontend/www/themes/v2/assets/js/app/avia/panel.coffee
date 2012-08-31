class AviaPanel
  constructor: ->
    @rt = ko.observable false
    @minimized = ko.observable false

    # Popup inputs
    @adults = ko.observable 2
    @childs = ko.observable 0
    @infants = ko.observable 4

    @overall = ko.computed =>
      @adults() + @childs() + @infants()

    @rt.subscribe (newValue) ->
      if newValue
        $('.tumblr .switch').animate {'left': '35px'}, 200
      else
        $('.tumblr .switch').animate {'left': '-1px'}, 200

    @minimized.subscribe (minimized) ->
      speed =  300
      heightSubHead = $('.sub-head').height()
 
      if !minimized
        $('.sub-head').animate {'margin-top' : '0px'}, speed
      else
        $('.sub-head').animate {'margin-top' : '-'+(heightSubHead-4)+'px'}, speed

    # FIXME:
    $ ->
      $('.how-many-man .popup').find('input').hover ->
        $(this).parent().find('.plusOne').show()
        $(this).parent().find('.minusOne').show()
      , ->
        $(this).parent().find('.plusOne').hide()
        $(this).parent().find('.minusOne').hide()
  
      $('.plusOne').hover ->
        $(this).addClass('active')
        $('.minusOne').addClass('active')
      , ->
        $(this).removeClass('active')
        $('.minusOne').removeClass('active')

      $('.minusOne').hover ->
        $(this).addClass('active');
        $('.plusOne').addClass('active')
      , ->
        $(this).removeClass('active')
        $('.plusOne').removeClass('active')

      # Placeholder-like behaviour for inputs
      $('.how-many-man .popup').find('input').focus ->
        $(@).attr 'rel', $(@).val()
        $(@).val '3'
        console.log $(@)
        $(@).trigger 'change'

      $('.how-many-man .popup').find('input').blur ->
        if $(@).val() == ''
          $(@).val $(@).attr 'rel' 
        $(@).trigger 'change'

  selectOneWay: =>
    @rt(false)

  selectRoundTrip: =>
    @rt(true)

  # Minimize button click handler
  minimize: ->
    if @minimized()
      @minimized(false)
    else
      @minimized(true)

  plusOne: (observable)->
    console.log observable
    e.preventDefault();
    var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
    var_valCount++;
    $(this).parent().find('input').val(var_valCount);
    changeAdultsCount();

  minusOne: (data)->
    var_valCount = Math.abs(parseInt( $(this).parent().find('input').val() ));
    var_valCount--;
    if (var_valCount < 1)
      var_valCount = 1;
    $(this).parent().find('input').val(var_valCount);

# TODO SIZE OF THE PEPOPLE COUNTER xN
# TODO on focus - save and hide current amount of pplz, return it on no/wrong input
# TODO minimal adults == 1
# <0 is no good either be it minus click or input!

###
$(function() {

function initPeoplesInputs() {
  $('.how-many-man .popup').find('input').eq(0).keyup(changeAdultsCount);
  $('.how-many-man .popup').find('input').eq(1).keyup(changeChildCount);
  $('.how-many-man .popup').find('input').eq(2).keyup(changeInfantCount);

  
 
}
});###