class PeopleSelector
  constructor: ->
    # helper flags for clicck outside of ppl panel handling
    @inside = false
    @inside2 = false
    @inside3 = false

  show: (context, event) =>
    el = $(event.currentTarget)
    if !el.hasClass('active')
      $(document.body).mousedown =>
        if @inside ||  @inside2 || @inside3
          return
        @close()
      $('.how-many-man .btn').addClass('active')
      $('.how-many-man .content').addClass('active')
      el = $('.how-many-man').find('.popup')
      el.addClass('active')
      coords = $('.how-many-man').offset()
      el.css 'top', coords.top +  $('.how-many-man').height() 
      el.css 'left', coords.left
      
    else
      @close()

  afterRender: =>
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

    $('.how-many-man').find('.popup').hover =>
      @inside = true
    , =>
      @inside = false

    $('.how-many-man .content').hover =>
      @inside2 = true
    , =>
      @inside2 = false

    $('.how-many-man .btn').hover =>
      @inside3 = true
    , =>
      @inside3 = false

  close: ->
    $(document.body).unbind 'mousedown'
    $('.how-many-man .btn').removeClass('active')
    $('.how-many-man .content').removeClass('active')

    $('.how-many-man').find('.popup').removeClass('active')


class Passengers extends PeopleSelector
  constructor: () ->
    @template = 'passengers-template'
    # Popup inputs
    @adults = ko.observable(1).extend({integerOnly: {min:1, max:6}})
    @children = ko.observable(0).extend({integerOnly: {min:0, max:4}})
    @infants = ko.observable(0).extend({integerOnly: {min:0, max:3}})
    
    @MAX_TRAVELERS = 9

    @children.subscribe (newValue) =>
      if newValue > @MAX_TRAVELERS - 1
        @children @MAX_TRAVELERS - 1
      @balanceTravelers ["adults", 'infants'], @

    @infants.subscribe (newValue) =>
      if newValue > @adults()
        @adults @infants()
      @balanceTravelers ["children", 'adults'], @

    @sum_children = ko.computed =>
      # dunno why but we have stange to string casting here
      @children()*1 + @infants()*1

    @overall = ko.computed =>
      @adults()*1 + @children()*1 + @infants()*1

    # Travelers constraits
    @adults.subscribe (newValue) =>
      if @infants() > @adults()
        @infants @adults()

      if newValue > @MAX_TRAVELERS
        @adults @MAX_TRAVELERS

      @balanceTravelers ["children", 'infants'], @
    @overall.subscribe (newValue) =>
      do resizePanel

  ###
  Balances number of travelers, using those which was not affected by most recent user change
  ###
  balanceTravelers: (others, model) =>
    if @overall() > @MAX_TRAVELERS
      # How many travelers we need to throw out
      delta = model.overall() - @MAX_TRAVELERS
      for prop in others
        if model[prop]() >= delta
          model[prop] model[prop]() - delta
          break
        else
          delta -= model[prop]()
          model[prop] 0


  plusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop](model[prop]()+1)

  minusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop] model[prop]()-1


# View model for a single room dropdown
class Roomers
  constructor: (@room, @index, @length ) ->
    @adults = @room.adults
    @children = @room.children
    @ages = @room.ages
    #@overall = 

  plusOne: (context, event) =>
    target = $(event.currentTarget).attr('rel')
    @[target] @[target]() + 1

  minusOne: (context, event) =>
    target = $(event.currentTarget).attr('rel')
    if @[target]() > 0
      @[target] @[target]() - 1

  last: =>
    return @index+1 == @length

# View Model for hotels people selector
# FIXME ME move me somewhere
class HotelPeopleSelector extends PeopleSelector
  constructor: (@sp)->
    super
    @template = 'roomers-template'
    @rawRooms = @sp.rooms
    @roomsView = ko.computed =>
      result = []
      current = []
      for item, index in @sp.rooms()
        if current.length == 2
          result.push current
          current = []
        # FIXME mem/handlers leak?
        r = new Roomers item, index, @sp.rooms().length
        r.addRoom = @addRoom
        r.removeRoom = @removeRoom
        current.push r
      result.push current
      return result
    @sp.overall.subscribe resizePanel


  addRoom: =>
    if @sp.rooms().length == 4
      return
    if @sp.overall() > 8
      return
    @sp.rooms.push new SpRoom(@sp)


  removeRoom: (roomer)=>
    # You should not be allower to remove last room left
    if @sp.rooms().length == 1
      return
    @sp.rooms.splice @sp.rooms.indexOf(roomer.room),1
