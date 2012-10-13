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

  priceHtml: =>
    if @selection() == null
      return "Не выбрано"
    return @price() + '<span class="rur">o</span>'

  minPriceHtml: =>
    @minPrice() + '<span class="rur">o</span>'

  maxPriceHtml: =>
    @maxPrice() + '<span class="rur">o</span>'


  savings: =>
    if @selection() == null
      return 0

    return 555

  rt: =>
    false

class ToursAviaResultSet extends TourEntry
  constructor: (raw, sp)->
    super
    @api = new AviaAPI
    @template = 'avia-results'
    @overviewTemplate = 'tours-overview-avia-ticket'
    @panel = new AviaPanel()
    @panel.handlePanelSubmit = @doNewSearch
    @panel.sp.fromObject sp
    @panel.original_template = @panel.template
    @panel.template = 'tours-panel-template'
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
      result.selected_best res.best | false
      @selection(res)
    @avia = true
    @results result

  doNewSearch: =>
    @api.search @panel.sp.url(), (data)=>
      @newResults data.flights.flightVoyages, data.searchParams

  # Overview VM
  overviewText: =>
    "Перелет " + @results().departureCity + ' &rarr; ' + @results().arrivalCity

  numAirlines: =>
    # FIXME FIXME FIXME
    @results().filters.airline.options().length

  minPrice: =>
    cheapest = _.reduce @results().data,
      (el1, el2)->
        if el1.price < el2.price then el1 else el2
      ,@results().data[0]
    cheapest.price

  maxPrice: =>
    mostExpensive = _.reduce @results().data,
      (el1, el2)->
        if el1.price > el2.price then el1 else el2
      ,@results().data[0]
    mostExpensive.price
    

  # End overview VM

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
    # FIXME SEARCH PARAMS
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

  timelineStart: =>
    source = @selection()
    if source == null
      source = @results().data[0]
    source.departureDate()

  rtTimelineStart: =>
    source = @selection()
    if source == null
      source = @results().data[0]
    source.rtDepartureDate()

  rt: =>
    source = @selection()
    if source == null
      source = @results().data[0]
    source.roundTrip

  timelineEnd: =>
    source = @selection()
    if source == null
      source = @results().data[0]
    source.arrivalDate()
        
  rt: =>
    @results().roundTrip
       
class ToursHotelsResultSet extends TourEntry
  constructor: (raw, @searchParams)->
    super
    @api = new HotelsAPI
    @panel = new HotelsPanel()
    @panel.handlePanelSubmit = @doNewSearch
    @panel.sp.fromObject @searchParams
    @panel.original_template = @panel.template
    @panel.template = 'tours-panel-template'
    @overviewTemplate = 'tours-overview-hotels-ticket'
    @template = 'hotels-results'

    @activeHotel = ko.observable 0
    @selection = ko.observable null
    @results = ko.observable()
    @data = {results: @results}

    @newResults raw, @searchParams

  newResults: (data, sp)=>
    result = new HotelsResultSet data, sp, @activeHotel
    result.tours true
    result.postInit()
    result.select = (hotel) =>
      hotel.off 'back'
      hotel.on 'back', =>
        @trigger 'setActive', @
      hotel.off 'select'
      hotel.on 'select', (roomData) =>
        @activeHotel  hotel.hotelId
        @selection roomData
      @trigger 'setActive', {'data':hotel, template: 'hotels-info-template'}
    # FIXME WTF
    @hotels = true
    @selection null
    @results result

  doNewSearch: =>
    @api.search @panel.sp.url(), (data)=>
      @newResults data.hotels, data.searchParams

  # Overview VM
  overviewText: =>
    @destinationText()

  numHotels: =>
    @results.data.length

  minPrice: =>
    @results.minPrice

  maxPrice: =>
    @results.maxPrice

  # end Overview VM

  # tours overview
  destinationText: =>
    "Отель в " + @searchParams.city

  price: =>
    if @selection() == null
      return 0

    @selection().roomSet.price
    
  additionalText: =>
    if @selection() == null
      return ""
    ", " + @selection().hotel.hotelName

  dateClass: =>
    'orange-two'

  dateHtml: =>
    result = '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth @results().checkIn
    result+='</div>'
    result+= '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth @results().checkOut
    result+= '</div>'

  timelineStart: =>
    @results().checkIn

  timelineEnd: =>
    @results().checkOut

class ToursResultSet
  constructor: (raw, @searchParams)->
    @data = ko.observableArray()
    for variant in raw.allVariants
      if !variant
        continue
      if variant.flights
        @data.push new ToursAviaResultSet variant.flights.flightVoyages, variant.searchParams
      else
        result = new ToursHotelsResultSet variant.hotels, variant.searchParams
        @data.push result
        result.on 'setActive', (entry)=>
          @setActive entry

    @timeline = new Timeline(@data)
    @selection = ko.observable @data()[1]
    @panel = ko.computed 
      read: =>
        if @selection().panel
          @panelContainer = @selection().panel
        @panelContainer.timeline = @timeline
        @panelContainer.setActiveTimelineAvia = @setActiveTimelineAvia
        @panelContainer.setActiveTimelineHotels = @setActiveTimelineHotels
        @panelContainer.selection = @selection

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


  setActive: (entry)=>
    @selection entry
    ko.processAllDeferredBindingUpdates()
    ResizeAvia()

  setActiveTimelineAvia: (entry)=>
    @selection entry.avia.item
    ko.processAllDeferredBindingUpdates()
    ResizeAvia()

  setActiveTimelineHotels: (entry)=>
    @selection entry.hotel.item
    ko.processAllDeferredBindingUpdates()
    ResizeAvia()


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
    ResizeAvia()

  showOverview: =>
    @setActive {template: 'tours-overview', data: @}



# Models for tour search params,
class DestinationSearchParams
  constructor: ->
    @city = ko.observable ''
    @dateFrom = ko.observable ''
    @dateTo = ko.observable ''

class RoomsSearchParams
  constructor: ->
    @adt = ko.observable 2
    @chd = ko.observable 0
    @chdAge = ko.observable false
    @cots = ko.observable false

# Used in TourPanel and search controller
class TourSearchParams extends SearchParams
  constructor: ->
    super()
    @startCity = ko.observable 'LED'
    @destinations = ko.observableArray []
    # FIXME copy paste from hotel search params
    @rooms = ko.observableArray [new SpRoom(@)]
    @overall = ko.computed =>
      result = 0
      for room in @rooms()
        result += room.adults()
        result += room.children()
      return result

    @returnBack = ko.observable 1

  url: ->
    result = 'tour/search?'
    params = []
    params.push 'start=' + @startCity()
    _.each @destinations(), (destination, ind) =>
      params.push 'destinations[' + ind + '][city]=' + destination.city()
      params.push 'destinations[' + ind + '][dateFrom]=' + moment(destination.dateFrom()).format('D.M.YYYY')
      params.push 'destinations[' + ind + '][dateTo]=' + moment(destination.dateTo()).format('D.M.YYYY')

    _.each @rooms(), (room, ind) =>
      params.push room.getUrl(ind)

    result += params.join "&"
    window.voyanga_debug "Generated search url for tours", result
    return result

  key: ->
    key = @startCity()
    _.each @destinations(), (destination) ->
      key += destination.city() + destination.dateFrom() + destination.dateTo()
    _.each @rooms(), (room) ->
      key += room.getHash()
    return key

  getHash: ->
    parts =  [@startCity(), @returnBack()]
    _.each @destinations(), (destination) ->
      parts.push destination.city()
      parts.push moment(destination.dateFrom()).format('D.M.YYYY')
      parts.push moment(destination.dateTo()).format('D.M.YYYY')
    parts.push 'rooms'
    _.each @rooms(), (room) ->
      parts.push room.getHash()

    hash = 'tours/search/' + parts.join('/') + '/'
    window.voyanga_debug "Generated hash for tour search", hash
    return hash

  fromList: (data)->
    window.voyanga_debug "Restoring TourSearchParams from list"
    @startCity data[0]
    @returnBack data[1]
    # FIXME REWRITE ME
    doingrooms = false
    @destinations([])
    @rooms([])
    for i in [2..data.length] by 3
      if data[i] == 'rooms'
        break
      console.log data[i], data[i+1], data[i+2]
      destination = new DestinationSearchParams()
      destination.city(data[i])
      destination.dateFrom(moment(data[i+1], 'D.M.YYYY').toDate())
      destination.dateTo(moment(data[i+2], 'D.M.YYYY').toDate())
      @destinations.push destination

    i = i + 1
    while i < data.length
      room = new SpRoom(@)
      room.fromList(data[i])
      @rooms.push room
      i++
    window.voyanga_debug 'Result', @

  fromObject: (data)->
    window.voyanga_debug "Restoring TourSearchParams from object"
    console.log data

    _.each data.destinations, (destination) ->
      destination = new DestinationSearchParams()
      destination.city(destination.city)
      destination.dateFrom(moment(destination.dateFrom, 'D.M.YYYY').toDate())
      destination.dateTo(moment(destination.dateTo, 'D.M.YYYY').toDate())
      @destinations.push destination

    _.each data.rooms, (room) ->
      room = new SpRoom(@)
      @rooms.push @room.fromObject(room)

    window.voyanga_debug 'Result', @

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
    if firstResult.isAvia()
      result+= dateUtils.formatHtmlDayShortMonth source.departureDate()
    else
      result+= dateUtils.formatHtmlDayShortMonth firstResult.results.checkIn

    result+='</div>'
    return result
