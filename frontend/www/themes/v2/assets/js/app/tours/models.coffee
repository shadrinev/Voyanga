class TourEntry
  constructor: ->
    # Mix in events
    _.extend @, Backbone.Events

  isAvia: =>
    return @avia
  isHotel: =>
    return @hotels

  price: =>
    if @selection() == null
      return 0
    @selection().price

  priceText: =>
    if @selection() == null
      return "Не выбрано"
    return @price() + '<span class="rur">o</span>'

  savings: =>
    if @selection() == null
      return 0

    return 555

  rt: =>
    false

class ToursAviaResultSet extends TourEntry
  constructor: (raw, sp)->
    @api = new AviaAPI
    @template = 'avia-results'
    @overviewTemplate = 'tours-overview-avia-ticket'
    # FIXME
    #new Searchparams...
    @panel = new AviaPanel()
    @panel.handlePanelSubmit = @doNewSearch
    @panel.sp.fromObject sp
    @results = ko.observable()
    @selection = ko.observable null
    @newResults raw, sp
    @data = {results: @results}

  newResults: (raw, sp)=>
    result = new AviaResultSet raw
    result.injectSearchParams sp
    result.postInit()
    result.recommendTemplate = 'avia-tours-recommend'
    result.tours = true
    result.select = (res)=>
      # FIXME looks retardely stupid
      if res.ribbon
        #it is actually recommnd ticket
        res = res.data
      result.selected_key res.key
      @selection(res)
    @avia = true
    # FIXME
    r = result.data[0]
    result.selected_key r.key
    @selection result.data[0]
    # END FIXME
    @results result

  doNewSearch: =>
    @api.search @panel.sp.url(), (data)=>
      @newResults data.flights.flightVoyages, data.searchParams


  overviewText: =>
    "Перелет " + @results().departureCity + ' &rarr; ' + @results().arrivalCity   

  destinationText: =>
    @results().departureCity + ' &rarr; ' + @results().arrivalCity   

  additionalText: =>
    if @selection() == null
      return ""
    if @rt()
      ""
    else
      ", " + @selection().departureTime() + ' - ' + @selection().arrivalTime()

  dateClass: =>
    if @rt() then 'blue-two' else 'blue-one'

  dateHtml: =>
    source = @selection()
    if source == null
      source = @results().data[0]
    result = '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth source.departureDate()
    result+='</div>'
    if @rt()
      result+= '<div class="day">'
      result+= dateUtils.formatHtmlDayShortMonth source.rtDepartureDate()
      result+= '</div>'
    return result
    
  rt: =>
    @results().roundTrip
       
class ToursHotelsResultSet extends TourEntry
  constructor: (raw, @searchParams)->
    super
    @overviewTemplate = 'tours-overview-hotels-ticket'
    @activeHotel = ko.observable 0
    @template = 'hotels-results'
    @results = new HotelsResultSet raw, @searchParams
    @results.tours true
    @results.select = (hotel) =>
      hotel.off 'back'
      hotel.on 'back', =>
        @trigger 'setActive', @
      hotel.off 'select'
      hotel.on 'select', (room) =>
        @activeHotel  hotel.hotelId
        @selection room

      hotel.active_result = @active_result
      @trigger 'setActive', {'data':hotel, template: 'hotels-info-template'}
    @data = {results: ko.observable(@results)}
    @hotels = true
    @selection = ko.observable null

  overviewText: =>
    @destinationText()

  # tours overview
  destinationText: =>
    "Отель в " + @searchParams.city

  price: =>
    if @selection() == null
      return 0

    @selection().room.price()

  additionalText: =>
    if @selection() == null
      return ""
    ", " + @selection().hotel.hotelName

  dateClass: =>
    'orange-two'

  dateHtml: =>
    result = '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth @results.checkIn
    result+='</div>'
    result+= '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth @results.checkOut
    result+= '</div>'

class ToursResultSet
  constructor: (raw)->
    @data = ko.observableArray()
    for variant in raw.allVariants
      if variant.flights
        @data.push new ToursAviaResultSet variant.flights.flightVoyages, variant.searchParams
      else
        result = new ToursHotelsResultSet variant.hotels, variant.searchParams
        @data.push result
        result.on 'setActive', (entry)=>
          @setActive entry
          
    @selection = ko.observable @data()[0]
    @panel = ko.computed 
      read: =>
        if @selection().panel
          @panelContainer = @selection().panel
        return @panelContainer

    @price = ko.computed =>
      sum = 0
      for item in @data()
        sum += item.price()
      return sum

    @savings = ko.computed =>
      sum = 0
      for item in @data()
        sum += item.savings()
      return sum

    @vm = new ToursOverviewVM @
    do @showOverview

  setActive: (entry)=>
    @selection entry
    ko.processAllDeferredBindingUpdates()
    ResizeAvia()

  showOverview: =>
    @setActive {template: 'tours-overview', data: @}

  removeItem: (item, event)=>
    event.stopPropagation()
    if @data().length <2
      return
    idx = @data.indexOf(item)
    console.log @data.indexOf(item), item, @selection()

    if idx ==-1
      return
    @data.splice(idx, 1)
    if item == @selection()
      @setActive @data()[0]

# decoupling some presentation logic from resultset
class ToursOverviewVM
  constructor: (@resultSet)->

  startCity: =>
    firstResult = @resultSet.data()[0]
    if firstResult.isAvia()
      firstResult.results().departureCity
    else
      'Бобруйск'


  dateClass: =>
    'blue-one'
  
  
  dateHtml: =>
    firstResult = @resultSet.data()[0]
    source = firstResult.selection()
    result = '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth source.departureDate()
    result+='</div>'
    return result
