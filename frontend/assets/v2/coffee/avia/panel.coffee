class AviaPanel extends SearchPanel
  constructor: ->
    super()
    @prevPanel = 'tours'
    @prevPanelLabel = 'Путешествия'
    @nextPanel = 'hotels'
    @nextPanelLabel = 'Только отели'
    @mainLabel = 'Поиск авиабилетов'
    @icon = 'fly-ico'

    @template = 'avia-panel-template'
    @sp = new AviaSearchParams()
    @passengers = @sp.passengers
    @departureDate = @sp.date
    @departureCity = @sp.dep
    @departureCityReadable = ko.observable ''
    @departureCityReadableGen = ko.observable ''
    @departureCityReadableAcc = ko.observable ''
    @departureCityReadablePre = ko.observable ''
    @rt = @sp.rt
    @rtDate = @sp.rtDate
    @arrivalCity = @sp.arr
    @arrivalCityReadable = ko.observable ''
    @arrivalCityReadableGen = ko.observable ''
    @arrivalCityReadableAcc = ko.observable ''
    @arrivalCityReadablePre = ko.observable ''
    @prefixText = 'Все направления<br>500+ авиакомпаний'
    @calendarActive = ko.observable(true)
    @selectionIndex = ko.observable ''
    @selectionIndex.subscribe(
      (newValue)=>
        if(newValue == 1 && @rt())
          if @departureDate()
            VoyangaCalendarStandart.checkCalendarValue(false)
            @departureDate('')
            @rtDate('')
    )

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

    @formNotFilled = ko.computed =>
      !@formFilled()

    @maximizedCalendar = ko.computed =>
      @departureCity() && @arrivalCity()

    @maximizedCalendar.subscribe (newValue) =>
      if !newValue
        return
      if @calendarActive()
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
      activeSearchPanel: @
      valuesDescriptions: ['Вылет туда', 'Вылет обратно', 'Туда и обратно']
      selectionIndex: @selectionIndex

    @calendarValue.subscribe =>
      if !VoyangaCalendarStandart.checkCalendarValue()
        window.setTimeout(
          =>
            VoyangaCalendarStandart.checkCalendarValue(true)
          , 100
        )

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
      result = "Выберите дату вылета "
      if @rt()
        arrow = ' ↔ '
      else
        arrow = ' → '
      console.log "updating"
      if (@departureCityReadable().length == 0)
        result = 'Выберите город вылета'
      else if (@arrivalCityReadable().length == 0)
        result = 'Выберите город прилёта'
      else if (@selectionIndex() == 0)
        result = 'Выберите дату вылета из ' + @departureCityReadableGen()
      else if ((@rt()) && (@selectionIndex() == 1))
        result = 'Выберите дату вылета из ' + @arrivalCityReadableGen()
      else
        result = @departureCityReadable() + arrow + @arrivalCityReadable() + ', ' + dateUtils.formatDayShortMonth(@departureDate())
        if @rt()
          result += ' - ' + dateUtils.formatDayShortMonth(@rtDate())
      result

  rtTumbler: (newValue) ->
    if newValue
      $('.tumblr .switch').animate {'left': '35px'}, 200
    else
      $('.tumblr .switch').animate {'left': '-1px'}, 200

  # calendar handler
  setDate: (values)=>
    if values.length
      if !@departureDate() || (moment(@departureDate()).format('YYYY-MM-DD') != moment(values[0]).format('YYYY-MM-DD'))
        @departureDate values[0]
      if @rt and values.length > 1
        if values[1] >= @departureDate()
          if !@rtDate() || (moment(@rtDate()).format('YYYY-MM-DD') != moment(values[1]).format('YYYY-MM-DD'))
            @rtDate values[1]
        else
          @rtDate ''

  ###
  # Click handlers
  ###
  selectOneWay: =>
    @rt(false)

  selectRoundTrip: =>
    @rt(true)

  handlePanelSubmit: =>
    if window.location.pathname.replace('/', '') != ''
      $('#loadWrapBgMin').show()
      window.location.href = '/#' + @sp.getHash()
      return


    _gaq.push(['_trackEvent','Avia_press_button_search', @sp.GAKey(), @sp.GAData()])

    app.navigate @sp.getHash(), {trigger: true}
    @minimizedCalendar(true)

  # FIXME decouple!
  navigateToNewSearch: ->
    if (@formNotFilled())
      el = $('div.innerCalendar').find('h1')
      Utils.flashMessage el
      return
    @handlePanelSubmit()
    @minimizedCalendar(true)

  returnRecommend: (context, event)->
    $('.recomended-content').slideDown()
    $('.order-hide').fadeIn()
    $(event.currentTarget).animate {top: '-19px'}, 500, null, ->
      ResizeAvia()

  afterRender: =>
    $ =>
      @sp.passengers.afterRender()
      # Initial state for tumbler
      @rtTumbler(@rt())
      $('.how-many-man .btn')
      if (@departureCity() && @arrivalCity().length > 0 && (!@departureDate() || (@rt() && (!@rtDate))))
        @minimizedCalendar(false)
    do resizePanel

$(document).on "autocompleted", "input.departureCity", ->
  $('input.arrivalCity.second-path').focus()

$(document).on "keyup change", "input.second-path", (e) ->
  firstValue = $(this).val()
  secondEl = $(this).siblings('input.input-path')
  if ((e.keyCode == 8) || (firstValue.length < 3))
    secondEl.val('')
