class HotelsPanel extends SearchPanel
  constructor: ->
    super
    @template = 'hotels-panel-template'
    # translates our flat rooms array to two array as per our view
    # essentially just provides ViewModel for @rooms field

    @sp = new HotelsSearchParams()
    @city = @sp.city
    @checkIn = @sp.checkIn
    @checkOut = @sp.checkOut
    @rooms = @sp.rooms
    @roomsView = ko.computed =>
      result = []
      current = []
      for item in @rooms()
        if current.length == 2
          result.push current
          current = []
        current.push item
      result.push current
      return result

    @addRoom = @sp.addRoom
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
      from: @checkIn()
      to: @checkOut()

  handlePanelSubmit: =>
    app.navigate @sp.getHash(), {trigger: true}
    @minimizedCalendar(true)


  # FIXME decouple!
  navigateToNewSearch: ->
    @handlePanelSubmit()
    @minimizedCalendar(true)


  setDate: (values)=>
    if values.length
      @checkIn values[0]
      if values.length > 1
        @checkOut values[1]
