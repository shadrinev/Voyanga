class SearchPanel
  constructor: ->
    @minimized = ko.observable false
    @minimizedCalendar = ko.observable true
    @calendarHidden = ko.observable @minimizedCalendar
    @calendarShadow = ko.observable @minimizedCalendar

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    @togglePanel @minimized()
    @toggleCalendar @minimizedCalendar()

    @minimized.subscribe (minimized) =>
      @togglePanel(minimized)

    @minimizedCalendar.subscribe (minimizedCalendar) =>
      @toggleCalendar(minimizedCalendar)

  togglePanel: (minimized) =>
    speed =  300
    heightSubHead = $('.sub-head').height()

    if !minimized
      $('.sub-head').animate {'margin-top' : '0px'}, speed
    else
      $('.sub-head').animate {'margin-top' : '-'+(heightSubHead-4)+'px'}, speed

  toggleCalendar: (minimizedCalendar) =>
    speed =  500
    heightSubHead = $('.sub-head').height()
    heightCalendar1 = $('.calenderWindow').height()
    heightCalendar2 = heightSubHead

    if !minimizedCalendar
      @calendarHidden(false)
      $('.calenderWindow .calendarSlide').animate {'top' : '0px'}
      $('.calenderWindow').animate {'height' : '341px'}, speed
    else
      @calendarShadow(true)
      $('.calenderWindow .calendarSlide').animate {'top' : '-341px'}
      $('.calenderWindow').animate {'height' : '0px'}, speed, () =>
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
