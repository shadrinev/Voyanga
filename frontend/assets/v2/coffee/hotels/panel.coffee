class HotelsPanel extends SearchPanel
  constructor: ->
    @template = 'hotels-panel-template'
    super()
    @sp = new HotelsSearchParams()
    @calendarHidden = ko.observable true
    @city = @sp.city
    @checkIn = @sp.checkIn
    @checkOut = @sp.checkOut
    @peopleSelectorVM = new HotelPeopleSelector @sp
    @cityReadable = ko.observable()
    @cityReadableAcc = ko.observable()
    @cityReadableGen = ko.observable()
    @calendarText = ko.computed =>
      "vibAR->" + @cityReadable()

    @formFilled = ko.computed =>
      if @checkIn().getDay
        cin = true
      else
        cin =(@checkIn().length>0)
      if @checkOut().getDay
        cout = true
      else
        cout =(@checkOut().length>0)

      result = @city() && cin && cout
      return result

    @maximizedCalendar = ko.computed =>
      @city().length > 0

    @maximizedCalendar.subscribe (newValue) =>
      if !newValue
        return
      if @formFilled()
        return
      @showCalendar()
      
    @calendarValue = ko.computed =>
      twoSelect: true
      hotels: true
      from: @checkIn()
      to: @checkOut()

  handlePanelSubmit: =>
    app.navigate @sp.getHash(), {trigger: true}
    @minimizedCalendar(true)

  checkInHtml: =>
    if @checkIn()
      return dateUtils.formatHtmlDayShortMonth @checkIn()
    return ''

  checkOutHtml: =>
    if @checkOut()
      return dateUtils.formatHtmlDayShortMonth @checkOut()
    return ''

  haveDates: =>
    reutn @checkOut() && @checkIn()

  # FIXME decouple!
  navigateToNewSearch: ->
    @handlePanelSubmit()
    @minimizedCalendar(true)


  setDate: (values)=>
    if values.length
      @checkIn values[0]
      if values.length > 1
        @checkOut values[1]
