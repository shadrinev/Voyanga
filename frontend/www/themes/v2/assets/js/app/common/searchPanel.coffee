class SearchPanel
  constructor: ->
    @minimized = ko.observable false
    @minimizedCalendar = ko.observable false
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

  togglePanel: (minimized) ->
    speed =  300
    heightSubHead = $('.sub-head').height()

    if !minimized
      $('.sub-head').animate {'margin-top' : '0px'}, speed
    else
      $('.sub-head').animate {'margin-top' : '-'+(heightSubHead-4)+'px'}, speed

  toggleCalendar: (minimizedCalendar) ->
    speed =  500

    heightSubHead = $('.sub-head').height()
    heightCalendar1 = $('.calenderWindow').height()
    heightCalendar2 = heightSubHead

    if !minimizedCalendar
      @calendarHidden(false)
      $('.calenderWindow').animate {'top' : (heightSubHead-4) + 'px'}, speed
    else
      @calendarShadow(true)
      @calendarHidden(true)
      $('.calenderWindow').animate {'top' : '-'+(heightCalendar1)+'px'}, speed, () =>
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
  minimizeCalendar: ->
    if @minimizedCalendar()
      @minimizedCalendar(false)
    else
      @minimizedCalendar(true)

  showCalendar: ->
    if @minimizedCalendar()
      @minimizedCalendar(false)

