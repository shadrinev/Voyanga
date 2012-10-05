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
      hotel.on 'select', (roomData) =>
        @activeHotel  hotel.hotelId
        @selection roomData
      @trigger 'setActive', {'data':hotel, template: 'hotels-info-template'}
    @data = {results: ko.observable(@results)}
    @hotels = true
    @selection = ko.observable null
  # FIXME
    hotel = @results.data[0]
    room = hotel.roomSets[0]
    @activeHotel  hotel.hotelId
    @selection {'roomSet': room, 'hotel': hotel}
    # END FIXME
    
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
    @destinations = ko.observableArray [new DestinationSearchParams()]
    @rooms = ko.observableArray [ new Roomers() ]

  addRoom: =>
    if @rooms().length == 4
      return
    @rooms.push new Roomers()

  url: ->
    result = 'tour/search?'
    params = []
    _params.push 'start=' + @startCity()
    _.each @destinations(), (destination, ind) =>
      params.push 'destinations[' + ind + '][city]=' + destination.city()
      params.push 'destinations[' + ind + '][dateFrom]=' + destination.dateFrom()
      params.push 'destinations[' + ind + '][dateTo]=' + destination.dateTo()

    params.push 'rooms[0][adt]=' + @adults()
    params.push 'rooms[0][chd]=' + @children()
    params.push 'rooms[0][chdAge]=0'
    if @infants>0
      params.push 'rooms[0][cots]=1'
    else
      params.push 'rooms[0][cots]=0'

    result += params.join "&"
    window.voyanga_debug "Generated search url for tours", result
    return result

  key: ->
    key = @startCity()
    _.each @destinations(), (destination) ->
      key += destination.city() + destination.dateFrom() + destination.dateTo()
    key += @adults()
    key += @children()
    key += @infants()
    return key

  getHash: ->
    parts =  [@startCity(), @adults(), @children(), @infants()]
    _.each @destinations(), (destination) ->
      parts.push destination.city()
      parts.push destination.dateFrom()
      parts.push destination.dateTo()
    hash = 'tour/search/' + parts.join('/') + '/'
    window.voyanga_debug "Generated hash for tour search", hash
    return hash

  fromList: (data)->
    window.voyanga_debug "Restoring TourSearchParams from list"
    @startCity data[0]
    @adults data[1]
    @children data[2]
    @infants data[3]
    j = 0
    for i in [4..data.length] by 3
      @destinations[j] = new DestinationSearchParams()
      @destinations[j].city(data[i])
      @destinations[j].dateFrom(moment(data[i+1], 'D.M.YYYY').toDate())
      @destinations[j].dateTo(moment(data[i+2], 'D.M.YYYY').toDate())
      j++

  fromObject: (data)->
    window.voyanga_debug "Restoring TourSearchParams from object"
    console.log data
    @adults data.adt
    @children data.chd
    @infants data.inf
    j = 0
    _.each data.destinations, (destination) ->
      @destinations[j] = new DestinationSearchParams()
      @destinations[j].city(destination.city)
      @destinations[j].dateFrom(moment(destination.dateFrom, 'D.M.YYYY').toDate())
      @destinations[j].dateTo(moment(destination.dateTo, 'D.M.YYYY').toDate())
      j++

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
    if firstResult.isAvia()
      result+= dateUtils.formatHtmlDayShortMonth source.departureDate()
    else
      result+= dateUtils.formatHtmlDayShortMonth firstResult.results.checkIn

    result+='</div>'
    return result