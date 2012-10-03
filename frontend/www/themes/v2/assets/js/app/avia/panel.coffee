MAX_TRAVELERS = 9
MAX_CHILDREN = 8

# Recursion work around
EXITED = true

###
Balances number of travelers, using those which was not affected by most recent user change
###
balanceTravelers = (others, model)->
  if model.overall() > MAX_TRAVELERS && EXITED
    EXITED = false
    # How many travelers we need to throw out
    delta = model.overall() - MAX_TRAVELERS
    for prop in others
      if model[prop]() >= delta
        model[prop] model[prop]() - delta
        break
      else
        delta -= model[prop]()
        model[prop] 0
  EXITED = true


class AviaPanel extends SearchPanel
  constructor: ->
    super()
    @template = 'avia-panel-template'
    window.voyanga_debug "AviaPanel created"
    @sp = new SearchParams()
    @departureDate = @sp.date
    @departureCity = @sp.dep
    @departureCityReadable = ko.observable ''
    @departureCityReadableGen = ko.observable ''
    @departureCityReadableAcc = ko.observable ''
    @rt = @sp.rt
    @rtDate = @sp.rtDate
    @arrivalCity = @sp.arr
    @arrivalCityReadable = ko.observable ''
    @arrivalCityReadableGen = ko.observable ''
    @arrivalCityReadableAcc = ko.observable ''

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    #helper to handle dispaying of calendar
    @fromChosen = ko.computed =>
      if @departureDate().getDay
        return true
      @departureDate().length > 0

    @rtFromChosen = ko.computed =>
      if !@rt()
        return false

      if @rtDate().getDay
        return true

      @rtDate().length > 0

    @formFilled = ko.computed =>
      result = @departureCity() && @arrivalCity() && @fromChosen()
      if @rt()
       result = result && @rtFromChosen()
      return result


    @maximizedCalendar = ko.computed =>
      @departureCity() && @arrivalCity()

    @maximizedCalendar.subscribe (newValue) =>
      if !newValue
        return
      if @rt() && !@rtFromChosen()
        @showCalendar()
        return
      if !@fromChosen()
        @showCalendar()
        return
        
    @calendarValue = ko.computed =>
      twoSelect: @rt()
      from: @departureDate()
      to: @rtDate()

    # Popup inputs
    @adults = @sp.adults
    @children = @sp.children
    @infants = @sp.infants

    @departureDateDay = ko.computed =>
      dateUtils.formatDay(@departureDate())

    @departureDateMonth = ko.computed =>
      dateUtils.formatMonth(@departureDate())

    @rtDateDay = ko.computed =>
      dateUtils.formatDay(@rtDate())

    @rtDateMonth = ko.computed =>
      dateUtils.formatMonth(@rtDate())

    # Travelers constraits
    @adults.subscribe (newValue) =>
      if @infants() > @adults()
        @infants @adults()

      if newValue > MAX_TRAVELERS
        @adults MAX_TRAVELERS

      balanceTravelers ["children", 'infants'], @


    @children.subscribe (newValue) =>
      if newValue > MAX_TRAVELERS - 1
        @children MAX_TRAVELERS - 1

      balanceTravelers ["adults", 'infants'], @

    @infants.subscribe (newValue) =>
      if newValue > @adults()
        @adults @infants()

      balanceTravelers ["children", 'adults'], @

    @sum_children = ko.computed =>
      # dunno why but we have stange to string casting here
      @children()*1 + @infants()*1

    @overall = ko.computed =>
      @adults()*1 + @children()*1 + @infants()*1

    @rt.subscribe @rtTumbler

    @calendarText = ko.computed =>
      result = "Выберите дату перелета "
      if ((@departureCityReadable().length>0) && (@arrivalCityReadable().length>0))
        result +=@departureCityReadable() + ' → ' + @arrivalCityReadable()
      else if ((@departureCityReadable().length==0) && (@arrivalCityReadable().length>0))
        result+=' в ' + @arrivalCityReadableAcc()
      else if ((@departureCityReadable().length>0) && (@arrivalCityReadable().length==0))
        result+=' из ' + @departureCityReadableGen()
      return result

  afterRender: =>
    super
    # Initial state for tumbler
    @rtTumbler(@rt())
    $('.how-many-man .btn')

  rtTumbler: (newValue) ->
    if newValue
      $('.tumblr .switch').animate {'left': '35px'}, 200
    else
      $('.tumblr .switch').animate {'left': '-1px'}, 200


  # calendar handler
  setDate: (values)=>
    if values.length
      @departureDate values[0]
      if values.length > 1
        @rtDate values[1]

  ###
  # Click handlers
  ###
  selectOneWay: =>
    @rt(false)

  selectRoundTrip: =>
    @rt(true)

  plusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop](model[prop]()+1)

  minusOne: (model, e)->
    prop = $(e.target).attr("rel")
    model[prop] model[prop]()-1

  handlePanelSubmit: =>
    app.navigate @sp.getHash(), {trigger: true}
    @minimizedCalendar(true)


  # FIXME decouple!
  navigateToNewSearch: ->
    @handlePanelSubmit()
    @minimizedCalendar(true)

    
  returnRecommend: (context, event)->
    $('.recomended-content').slideDown()
    $('.order-hide').fadeIn();
    $(event.currentTarget).animate {top : '-19px'}, 500, null, ->
      ResizeAvia()

# FIXME WATAFAC
$(document).on "autocompleted", "input.departureCity", ->
  $('input.arrivalCity.second-path').focus()

$(document).on "keyup change", "input.second-path", (e) ->
  firstValue = $(this).val()
  secondEl = $(this).siblings('input.input-path')
  if ((e.keyCode==8) || (firstValue.length<3))
    secondEl.val('')

