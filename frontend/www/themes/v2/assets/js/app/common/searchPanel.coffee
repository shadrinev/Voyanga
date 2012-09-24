class SearchPanel
  constructor: ->
    @minimized = ko.observable false
    @minimizedCalendar = ko.observable false

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
    speed =  300

    heightSubHead = $('.sub-head').height()
    heightCalendar = $('.calenderWindow').height() + heightSubHead

    if !minimizedCalendar
      $('.calenderWindow').animate {'top' : (heightSubHead-4) + 'px'}, speed
    else
      $('.calenderWindow').animate {'top' : '-'+(heightCalendar-4)+'px'}, speed

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
