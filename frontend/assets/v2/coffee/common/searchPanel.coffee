class SearchPanel
  constructor: (hideCalendar = true,fromTourPanel = false) ->
    @minimized = ko.observable !hideCalendar
    @minimizedCalendar = ko.observable hideCalendar
    @calendarHidden = ko.observable @minimizedCalendar
    @calendarShadow = ko.observable @minimizedCalendar
    @prevSearchPanel = ko.observable (null)
    @nextSearchPanel = ko.observable (null)

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    @togglePanel @minimized(),fromTourPanel
    @toggleCalendar @minimizedCalendar(), true

    @toggleSubscribers = @minimized.subscribe (minimized) =>
      @togglePanel(minimized)

    @minimizedCalendar.subscribe (minimizedCalendar) =>
      @toggleCalendar(minimizedCalendar)

  togglePanel: (minimized,fromTourPanel = false) =>
    if(!fromTourPanel)
      if minimized
        console.log('hidePanel1')
      else
        console.log('showPanel1')
      speed =  300
      heightSubHead = $('.sub-head').height()
      console.log('change oSubHead', @,minimized)
      if !minimized
        $('.sub-head').animate {'margin-top' : '-5px'}, speed
      else
        $('.sub-head').animate {'margin-top' : '-'+(heightSubHead-4)+'px'}, speed

  toggleCalendar: (minimizedCalendar,initialize = false) =>
    speed =  500
    heightSubHead = $('.sub-head').height()
    heightCalendar1 = $('.calenderWindow').height()
    heightCalendar2 = heightSubHead
    console.log('toggle calendarRR')

    if !minimizedCalendar
      ResizeAvia()
      @calendarHidden(false)
      if !initialize
        $('.calenderWindow .calendarSlide').animate {'top' : '0px'}
        $('.calenderWindow').animate {'height' : '341px'}, speed
    else
      ResizeAvia()
      @calendarShadow(true)
      if !initialize
        @calendarShadow(true)
        $('.calenderWindow .calendarSlide').animate {'top' : '-341px'}
        $('.calenderWindow').animate {'height' : '0px'}, speed, () =>
          @calendarHidden(true)
          @calendarShadow(false)
      else
        @calendarHidden(true)
        @calendarShadow(false)


  # Minimize button click handler
  minimize: =>
    if @minimized()
      @minimized(false)
      @minimizedCalendar(@oldCalendarState)
    else
      @minimized(true)
      @oldCalendarState = @minimizedCalendar()
      if !@minimizedCalendar()
        @minimizedCalendar(true)

  # Minimize button click handler
  minimizeCalendar: =>
    if @minimizedCalendar()
      @minimizedCalendar(false)
    else
      @minimizedCalendar(true)

  showCalendar: =>
    $('.calenderWindow').show()
    if @minimizedCalendar()
      @minimizedCalendar(false)

  handlePanelSubmit: =>
    app.navigate @sp.getHash(), {trigger: true}
    @minimizedCalendar(true)


  afterRender: =>
    throw "Implement me"