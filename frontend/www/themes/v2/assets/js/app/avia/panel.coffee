MAX_TRAVELERS = 9

class AviaPanel
  constructor: ->
    @rt = ko.observable false
    @minimized = ko.observable false

    # Popup inputs
    @adults = ko.observable(1).extend({integerOnly: true})
    @children = ko.observable(0).extend({integerOnly: true})
    @infants = ko.observable(0).extend({integerOnly: true})

    @sum_children = ko.computed =>
      # dunno why but we have stange to string casting here
      @children()*1 + @infants()*1

    @overall = ko.computed =>
      @adults()*1 + @children()*1 + @infants()*1

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

      $('.adults,.childs,.small-childs').hover null,   ->
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
        $(@).val('')

      $('.how-many-man .popup').find('input').blur ->
        if $(@).val() == ''
          $(@).val $(@).attr 'rel'
        $(@).trigger 'change'
        # FIXME move to extender ?
        # FIXME implement better logic depending on which field being edited
        if _this.adults() == 0
          _this.adults(1)
        if _this.overall() > MAX_TRAVELERS
          _this.adults(MAX_TRAVELERS)
          _this.children(0)
          _this.infants(0)

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

  plusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop](model[prop]()+1)

  minusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop](model[prop]()-1)

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
