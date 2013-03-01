class SearchPanel
  constructor: (hideCalendar = true, fromTourPanel = false) ->
    @minimized = ko.observable !hideCalendar
    @minimizedCalendar = ko.observable hideCalendar
    @calendarHidden = ko.observable @minimizedCalendar
    @calendarShadow = ko.observable @minimizedCalendar
    @prevSearchPanel = ko.observable (null)
    @nextSearchPanel = ko.observable (null)
    @aPanelId = Math.floor(Math.random() * 10000)

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    @togglePanel @minimized(), fromTourPanel
    @toggleCalendar @minimizedCalendar(), true

    @toggleSubscribers = @minimized.subscribe (minimized) =>
      @togglePanel(minimized)

    @oldCalendar = null;

#    console.error("NEWH PAWNL")

    @minimizedCalendar.subscribe (minimizedCalendar) =>
      if @oldCalendar? && @oldCalendar == minimizedCalendar
        return
#      console.log "Y NIGA Y", @oldCalendar, minimizedCalendar
      @toggleCalendar(minimizedCalendar)
      @oldCalendar = minimizedCalendar

  togglePanel: (minimized, fromTourPanel = false) =>
    if(!fromTourPanel)
      speed =  300
      heightSubHead = dimMemo.getHeight('.sub-head')
      if !minimized
        $('.sub-head').animate {'margin-top': '0px'}, speed
      else
        $('.sub-head').animate {'margin-top': '-' + (heightSubHead - 4) + 'px'}, speed


  toggleCalendar: (minimizedCalendar, initialize = false) =>
    speed =  500
#    console.error "HEAR WE GO"
#    console.trace("toggle")
    heightSubHead = dimMemo.getHeight('.sub-head')
    heightCalendar1 = 0 #$('.calenderWindow').height()
    heightCalendar2 = heightSubHead
    if !minimizedCalendar
      @calendarHidden(false)
      if !initialize
        ResizeAvia()
        $('.calenderWindow .calendarSlide').animate {'top': '0px'}
        $('.calenderWindow').animate {'height': '341px'}, speed
    else
      @calendarShadow(true)
      if !initialize
        ResizeAvia()
        @calendarShadow(true)
        $('.calenderWindow .calendarSlide').animate {'top': '-341px'}
        $('.calenderWindow').animate {'height': '0px'}, speed, () =>
          @calendarHidden(true)
          @calendarShadow(false)
      else
        $('.calenderWindow .calendarSlide').css {'top': '-341px'}
        $('.calenderWindow').css {'height': '0px'}
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
    ch = !$('.calenderWindow').is(':visible')
    console.error('show calend params', ch, (ch == 0), $('.calenderWindow').css('height'), $('.calenderWindow .calendarSlide').css('top'))
    if(true)
      $('.calenderWindow').show()
      console.log('show calendar')
      #VoyangaCalendarStandart.panel.notifySubscribers(VoyangaCalendarStandart.panel())
      VoyangaCalendarStandart.scrollToDate(VoyangaCalendarStandart.scrollDate, true)
      #@calendarValue.notifySubscribers(@calendarValue())
      if @minimizedCalendar()
        @minimizedCalendar(false)

  handlePanelSubmit: =>
    app.navigate @sp.getHash(), {trigger: true}
    @minimizedCalendar(true)


  afterRender: =>
    throw "Implement me"