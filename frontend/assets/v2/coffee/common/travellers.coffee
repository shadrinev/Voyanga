class Travellers
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
      $('.how-many-man').find('.popup').addClass('active')
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


class Passengers extends Travellers
  constructor: () ->
    @template = 'passengers-template'
    # Popup inputs
    @adults = ko.observable 1
    @children = ko.observable 0
    @infants = ko.observable 0

    @MAX_TRAVELERS = 9
    @MAX_CHILDREN = 8

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


  afterRender: =>
    super()

  plusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop](model[prop]()+1)

  minusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop] model[prop]()-1

  close: ->
    $(document.body).unbind 'mousedown'
    $('.how-many-man .btn').removeClass('active')
    $('.how-many-man .content').removeClass('active')
    $('.how-many-man').find('.popup').removeClass('active')


class Roomers extends Travellers
  constructor: (item) ->
    super()
    @template = 'roomers-template'
    @adults = ko.observable 1
    @children = ko.observable 0
    @ages = ko.observableArray()

    if item
      parts = item.split(':')
      @adults = ko.observable parts[0]
      @children = ko.observable parts[1]
      for i in [0..@children]
        ages.push ko.observable parts[2+i]
      @children.subscribe (newValue)=>
        if @ages().length < newValue
          for i in [0..(newValue-@ages().length-1)]
            @ages.push ko.observable 12
            console.log "$# PUSHING", i
        else if @ages().length > newValue
          @ages.splice(newValue)
        ko.processAllDeferredBindingUpdates()

  plusOne: (context, event) =>
    target = $(event.currentTarget).attr('rel')
    @[target] @[target]() + 1

  minusOne: (context, el) =>
    target = $(event.currentTarget).attr('rel')
    if @[target]() > 0
      @[target] @[target]() - 1

  getHash: =>
    parts = [@adults(), @children()]
    for age in @ages()
      parts.push age
    return parts.join(':')

  getUrl: (i)=>
    # FIXME FIMXE FIMXE
    return "rooms[#{i}][adt]=" + @adults() + "&rooms[#{i}][chd]=" + @children() + "&rooms[#{i}][chdAge]=0&rooms[#{i}][cots]=0"