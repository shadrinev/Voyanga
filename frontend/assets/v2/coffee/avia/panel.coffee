class AviaPanel extends SearchPanel
  constructor: ->
    super()
    @prevPanel = 'tours'
    @nextPanel = 'hotels'
    @icon = 'fly-ico';
    @indexMode = ko.observable true

    @template = 'avia-panel-template'
    window.voyanga_debug "AviaPanel created"
    @sp = new AviaSearchParams()
    @passengers = @sp.passengers
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

    @show = @passengers.show

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
      hotels: false

    @departureDateDay = ko.computed =>
      dateUtils.formatDay(@departureDate())

    @departureDateMonth = ko.computed =>
      dateUtils.formatMonth(@departureDate())

    @rtDateDay = ko.computed =>
      dateUtils.formatDay(@rtDate())

    @rtDateMonth = ko.computed =>
      dateUtils.formatMonth(@rtDate())

    @rt.subscribe @rtTumbler

    # Initial state for tumbler
    @rtTumbler(@rt())
    $('.how-many-man .btn')

    @calendarText = ko.computed =>
      result = "Выберите дату перелета "
      if ((@departureCityReadable().length>0) && (@arrivalCityReadable().length>0))
        result +=@departureCityReadable() + ' → ' + @arrivalCityReadable()
      else if ((@departureCityReadable().length==0) && (@arrivalCityReadable().length>0))
        result+=' в ' + @arrivalCityReadableAcc()
      else if ((@departureCityReadable().length>0) && (@arrivalCityReadable().length==0))
        result+=' из ' + @departureCityReadableGen()
      return result

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

  afterRender: =>
    $ =>
      @sp.passengers.afterRender()
      # Initial state for tumbler
      @rtTumbler(@rt())
      $('.how-many-man .btn')

$(document).on "autocompleted", "input.departureCity", ->
  $('input.arrivalCity.second-path').focus()

$(document).on "keyup change", "input.second-path", (e) ->
  firstValue = $(this).val()
  secondEl = $(this).siblings('input.input-path')
  if ((e.keyCode==8) || (firstValue.length<3))
    secondEl.val('')
