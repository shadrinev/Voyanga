# FIXME use mixins for most getters(?)

# Atomic journey unit.
class FlightPart
  constructor: (part)->
    @departureDate = new Date(part.datetimeBegin)
    @arrivalDate = new Date(part.datetimeEnd)
    @departureCity = part.departureCity
    @departureAirport = part.departureAirport
    @arrivalCity = part.arrivalCity
    @arrivalCityPre = part.arrivalCityPre
    @arrivalAirport = part.arrivalAirport
    @_duration = part.duration
    @transportAirline = part.transportAirline
    @transportAirlineName = part.transportAirlineNameEn
    @flightCode = part.transportAirline + ' ' + part.flightCode
    @stopoverLength = 0

  departureTime: ->
    dateUtils.formatTime @departureDate

  arrivalTime: ->
    dateUtils.formatTime @arrivalDate

  duration: ->
    dateUtils.formatDuration @_duration

  # calculate stopover length to given anotherPart
  calculateStopoverLength: (anotherPart) ->
    @stopoverLength = Math.floor((anotherPart.departureDate.getTime() - @arrivalDate.getTime())/1000)

  stopoverText: -> 
    dateUtils.formatDuration @stopoverLength
    
  

class Voyage #Voyage Plus loin que la nuit et le jour
  constructor: (flight) ->
    # Wrap flight parts in model
    @parts = []
    for part in flight.flightParts
      @parts.push new FlightPart(part)

    @stopoverLength = 0
    @direct = @parts.length == 1
    if ! @direct
      for part, index in @parts
        if index < (@parts.length - 1)
          part.calculateStopoverLength @parts[index+1]
        @stopoverLength += part.stopoverLength

    # FIXME is this  utc?
    @departureDate = new Date(flight.departureDate)
    @arrivalDate = new Date(@parts[@parts.length-1].arrivalDate)

    @_duration = flight.fullDuration

    @departureAirport = @parts[0].departureAirport
    @arrivalAirport = @parts[@parts.length-1].arrivalAirport

    @departureCity = flight.departureCity
    @arrivalCity = flight.arrivalCity

    @departureCityPre = flight.departureCityPre
    @arrivalCityPre = flight.arrivalCityPre


    @_backVoyages = []
    @activeBackVoyage = ko.observable()
    @visible = ko.observable true

  # returns our sort key
  departureInt: ->
    @departureDate.getTime()

  # Helper function, returns hash to check equality for first flight
  hash: ->
    @departureTime() + @arrivalTime()

  # pushes available back flight variants
  push: (voyage)->
    @_backVoyages.push voyage

  stacked: ->
    result = false
    count = 0
    for voyage in @_backVoyages
      if voyage.visible()
        count++;
      if count > 1
        result = true
        break
    console.log result
    return result

  departureDayMo: ->
    dateUtils.formatDayMonth @departureDate

  departurePopup: ->
    dateUtils.formatDayMonthWeekday @departureDate

  departureTime: ->
    dateUtils.formatTime @departureDate

  departureTimeNumeric: ->
    dateUtils.formatTimeInMinutes @departureDate

  arrivalDayMo: ->
    dateUtils.formatDayMonth @arrivalDate

  arrivalTime: ->
    dateUtils.formatTime @arrivalDate

  arrivalTimeNumeric: ->
    dateUtils.formatTimeInMinutes @arrivalDate


  duration: ->
    dateUtils.formatDuration @_duration

  stopoverText: ->
    if @direct
      return "Без пересадок"
    result = []
    for part in @parts[0..-2]
      result.push part.arrivalCityPre
    "Пересадка в " + result.join(', ')

  stopsRatio: ->
    result = []
    if @direct
      return result
    for part in @parts[0..-2]
      result.push Math.ceil part.duration/@_duration*80
    for data, index in result
      if index > 1
        result[index] = result[index-1]+data
      else
        result[index] = data + 10

    htmlResult = ""
    for left in result
      htmlResult += '<span class="cup" style="left: ' + left + '%;"></span>'
    htmlResult += '<span class="down"></span>'

    return htmlResult

  sort: ->
    #console.log "SORTENG "
    @_backVoyages.sort((a,b) -> a.departureInt() - b.departureInt())
    @activeBackVoyage(@_backVoyages[0])

  filter: (filters)->
    result = true
    match_departure_time = false
    if filters.departureTime.timeFrom <= @departureTimeNumeric() && filters.departureTime.timeTo >= @departureTimeNumeric()
      match_departure_time = true
    if !match_departure_time
      console.log 'filt by DT'
    result = result && match_departure_time

    match_arrival_time = false
    if filters.arrivalTime.timeFrom <= @arrivalTimeNumeric() && filters.arrivalTime.timeTo >= @arrivalTimeNumeric()
      match_arrival_time = true
    if !match_arrival_time
      console.log 'filt by AT'
    result = result && match_arrival_time

    if filters.onlyDirect == '1'
      result = result && @direct
    if !result
      console.log 'filt by on Dir'

    if filters.onlyShort
      result = result && (@stopoverLength <= 7200)


    if @_backVoyages.length > 0
      haveBack = false
      console.log('have back')
    else
      haveBack = true
    for rtVoyage in @_backVoyages
      thisBack = true
      match_departure_time = false
      if filters.departureTimeReturn.timeFrom <= rtVoyage.departureTimeNumeric() && filters.departureTimeReturn.timeTo >= rtVoyage.departureTimeNumeric()
        match_departure_time = true
      thisBack = thisBack && match_departure_time

      if filters.onlyDirect == '1'
        thisBack = thisBack && rtVoyage.direct

      if filters.onlyShort
        thisBack = thisBack && (rtVoyage.stopoverLength <= 7200)

      match_arrival_time = false
      if filters.arrivalTimeReturn.timeFrom <= rtVoyage.arrivalTimeNumeric() && filters.arrivalTimeReturn.timeTo >= rtVoyage.arrivalTimeNumeric()
        match_arrival_time = true

      thisBack = thisBack && match_arrival_time
      rtVoyage.visible(thisBack)
      if thisBack && !haveBack
        haveBack = true
        @activeBackVoyage(rtVoyage)
    if(!haveBack)
      console.log('filtred by back')
    result = result && haveBack
    if(!result)
      console.log 'filtered!!!'
    @visible(result)



#
# Coomon parts of StackedVoyage
#
class AviaResult
  constructor: (data) ->
    # Mix in events
    _.extend @, Backbone.Events


    flights = data.flights
    #! FIXME should magically work w/o ceil
    @price = Math.ceil(data.price)
    @_stacked = false
    @roundTrip = flights.length == 2
    @visible = ko.observable true

    @airline = data.valCompany
    @airlineName = data.valCompanyNameEn
    @serviceClass = data.serviceClass

    @activeVoyage = new Voyage(flights[0])
    if @roundTrip
      @activeVoyage.push new Voyage(flights[1])
    @voyages = []
    @voyages.push @activeVoyage
    @activeVoyage = ko.observable(@activeVoyage)

    @stackedMinimized = ko.observable true
    @rtStackedMinimized = ko.observable true


    # Generate proxy getters
    fields = ['departureCity', 'departureAirport', 'departureDayMo', 'departurePopup', 'departureTime', 'arrivalCity',
              'arrivalAirport', 'arrivalDayMo', 'arrivalTime', 'duration', 'direct', 'stopoverText', 'departureTimeNumeric', 'arrivalTimeNumeric',
              'hash', 'stopsRatio']

    for name in fields
      @[name] = ((name) =>
                  ->
                    field = @activeVoyage()[name]
                    if (typeof field) == 'function'
                      return field.apply @activeVoyage()
                    field
                )(name)
      # rt
      rtName = 'rt' + name.charAt(0).toUpperCase() + name.slice(1);
      @[rtName] = ((name) =>
                    ->
                      field = @activeVoyage().activeBackVoyage()[name]
                      if (typeof field) == 'function'
                        return field.apply @activeVoyage().activeBackVoyage()
                      field
                  )(name)

  filter: (filters) ->
      match_ports = true
      # FIXME UNDERSCORE
      match_ports_arr =
        'departure': filters.airports.departure.length == 0
        'arrival': filters.airports.arrival.length == 0
      for key in ['departure', 'arrival']
#         if @roundTrip
#            fields.push 'rtDepartureAirport'
           #    fields.push 'rtArrivalAirport'
        if filters.airports[key].indexOf(@[key+'Airport']()) >= 0
          match_ports_arr[key] = true
      if @roundTrip
        # swap arrival/departure for RT
        if filters.airports['arrival'].indexOf(@rtDepartureAirport()) >= 0
          match_ports_arr['departure'] and= true
        if filters.airports['departure'].indexOf(@rtArrivalAirport()) >= 0
          match_ports_arr['arrival'] and= true

      match_ports = match_ports_arr['arrival'] && match_ports_arr['departure']
       

      service_class = true
      if filters.serviceClass == 'A'
        service_class = @serviceClass == 'E'
      else
        service_class = @serviceClass == 'B' || @serviceClass == 'F'

      some_visible = false
      for voyage in @voyages
        voyage.filter(filters)
        if(!some_visible && voyage.visible())
          some_visible = true
          @activeVoyage(voyage)

      if filters.airlines.length == 0
        match_lines = true
      else
        match_lines = false
      if !match_lines && filters.airlines.indexOf(@airline) >= 0
        match_lines = true

      @visible(match_ports&&match_lines&&some_visible&&service_class)

  stacked: ->
    result = false
    count = 0
    for voyage in @voyages
      if voyage.visible()
        count++;
      if count > 1
        result = true
        break
    return result

  rtStacked: ->
    result = false
    count = 0
    for voyage in @activeVoyage()._backVoyages
      if voyage.visible()
        count++;
      if count > 1
        result = true
        break
    return result

  push: (data) ->
    @_stacked = true
    newVoyage = new Voyage(data.flights[0])
    if @roundTrip
      backVoyage = new Voyage(data.flights[1])
      newVoyage.push(backVoyage)
      result = _.find @voyages, (voyage) -> voyage.hash()==newVoyage.hash()
      if result
        result.push(backVoyage)
        return
    @voyages.push newVoyage

  chooseStacked: (voyage) =>
    window.voyanga_debug "Choosing stacked voyage", voyage
    hash = @activeVoyage().activeBackVoyage().hash()
    @activeVoyage(voyage)
    backVoyage = _.find voyage._backVoyages, (el)-> el.hash() == hash
    if backVoyage
      @activeVoyage().activeBackVoyage(backVoyage)

  # < > Buttons on recommended/cheapest ticket
  choosePrevStacked: =>
    active_index = 0
    for voyage, index in @voyages
      if voyage.hash() == @hash()
        active_index = index
    if active_index == 0
      return
    @activeVoyage @voyages[active_index-1]

  chooseNextStacked: =>
    active_index = 0
    for voyage, index in @voyages
      if voyage.hash() == @hash()
        active_index = index
    if active_index == @voyages.length-1
      return
    @activeVoyage @voyages[active_index+1]

  chooseRtStacked: (voyage) =>
    window.voyanga_debug "Choosing RT stacked voyage", voyage
    @activeVoyage().activeBackVoyage(voyage)

  # FIXME we can reuse code if we`ll pass voyages as method argument
  # < > Buttons on recommended/cheapest ticket
  choosePrevRtStacked: =>
    active_index = 0
    rtVoyages = @rtVoyages()
    for voyage, index in rtVoyages
      if voyage.hash() == @rtHash()
        active_index = index
    if active_index == 0
      return
    @activeVoyage().activeBackVoyage(rtVoyages[active_index-1])

  chooseNextRtStacked: =>
    active_index = 0
    rtVoyages = @rtVoyages()
    for voyage, index in rtVoyages
      if voyage.hash() == @rtHash()
        active_index = index
    if active_index == rtVoyages.length-1
      return
    @activeVoyage().activeBackVoyage(rtVoyages[active_index+1])

  # Handler for Списком link
  minimizeStacked: =>
    @stackedMinimized !@stackedMinimized()

  minimizeRtStacked: =>
    @rtStackedMinimized !@rtStackedMinimized()

  rtVoyages: ->
      @activeVoyage()._backVoyages

  sort: ->
    @voyages.sort((a,b) -> a.departureInt() - b.departureInt())
    if @roundTrip
      _.each(@voyages, (x)-> x.sort() )
    @activeVoyage(@voyages[0])

  # Shows popup with detailed info about given result
  showDetails: =>
    window.voyanga_debug "Setting popup result", @
    @trigger "popup", @
    $('body').prepend('<div id="popupOverlay"></div>')

    $('#avia-body-popup').show()
    ko.processAllDeferredBindingUpdates()

    SizeBox('avia-popup-body');
    ResizeBox('avia-popup-body');

    $('#popupOverlay').click =>
      @closeDetails()

  # Hide popup with detailed info about given result
  closeDetails: =>
    window.voyanga_debug "Hiding popup"
    $('#avia-body-popup').hide()
    $('#popupOverlay').remove()



#
# Result container
# Stacks them by price and company
#
class AviaResultSet
  constructor: (rawVoyages) ->
    @_results = {}

    for flightVoyage in rawVoyages
      key = flightVoyage.price + "_" + flightVoyage.valCompany
      if @_results[key]
        @_results[key].push flightVoyage
      else
        result =  new AviaResult flightVoyage
        @_results[key] = result
        result.key = key
        result.on "popup", (data)=>
          @popup data

    @cheapest = ko.observable()
    # We need array for knockout to work right
    @data = []

    @numResults = ko.observable 0

    @airports = []
    @departureAirports = []
    @arrivalAirports = []
    @airlines = []

    @timeLimits = {
      'departureFromTime':1440,
      'departureToTime':0,
      'departureFromToTimeActive':ko.observable('0;1440'),
      'arrivalFromTime':1440,
      'arrivalToTime':0,
      'arrivalFromToTimeActive':ko.observable('0;1440'),
      'departureFromTimeReturn':1440,
      'departureToTimeReturn':0,
      'departureFromToTimeReturnActive':ko.observable('0;1440'),
      'arrivalFromTimeReturn':1440,
      'arrivalToTimeReturn':0,
      'arrivalFromToTimeReturnActive':ko.observable('0;1440'),
    }

    # Temporary
    _airports = {}
    _departureAirports = {}
    _arrivalAirports = {}
    _airlines = {}

    for key, result of @_results
      result.sort()
      @data.push result
      _airlines[result.airline] = result.airlineName
      _departureAirports[result.departureAirport()]=1
      _arrivalAirports[result.arrivalAirport()]=1

      #console.log(result.departureTimeNumeric())
      if result.roundTrip
        _arrivalAirports[result.rtDepartureAirport()]=1
        _departureAirports[result.rtArrivalAirport()]=1
      for voyage in result.voyages
        if voyage.departureTimeNumeric() < @timeLimits.departureFromTime
          @timeLimits.departureFromTime = voyage.departureTimeNumeric()
        if voyage.departureTimeNumeric() > @timeLimits.departureToTime
          @timeLimits.departureToTime = voyage.departureTimeNumeric()
        if voyage.arrivalTimeNumeric() < @timeLimits.arrivalFromTime
          @timeLimits.arrivalFromTime = voyage.arrivalTimeNumeric()
        if voyage.arrivalTimeNumeric() > @timeLimits.arrivalToTime
          @timeLimits.arrivalToTime = voyage.arrivalTimeNumeric()
        if result.roundTrip
          for rtVoyage in voyage._backVoyages
            if rtVoyage.departureTimeNumeric() < @timeLimits.departureFromTimeReturn
              @timeLimits.departureFromTimeReturn = rtVoyage.departureTimeNumeric()
            if rtVoyage.departureTimeNumeric() > @timeLimits.departureToTimeReturn
              @timeLimits.departureToTimeReturn = rtVoyage.departureTimeNumeric()
            if rtVoyage.arrivalTimeNumeric() < @timeLimits.arrivalFromTimeReturn
              @timeLimits.arrivalFromTimeReturn = rtVoyage.arrivalTimeNumeric()
            if rtVoyage.arrivalTimeNumeric() > @timeLimits.arrivalToTimeReturn
              @timeLimits.arrivalToTimeReturn = rtVoyage.arrivalTimeNumeric()





    for key, foo of _departureAirports
      @departureAirports.push {'name':key, 'active': ko.observable 0 }

    for key, foo of _arrivalAirports
      @arrivalAirports.push {'name':key, 'active': ko.observable 0 }

    for key, foo of _airlines
      @airlines.push {'name':key,'visibleName':foo, 'active': ko.observable 0 }

    @_airportsFilters = ko.computed =>
          result = {'departure':[], 'arrival':[]}
          for port in @departureAirports
            if port.active()
              result['departure'].push port.name
          for port in @arrivalAirports
            if port.active()
              result['arrival'].push port.name
          return result

    @_airlinesFilters = ko.computed =>
      result = []
      for line in @airlines
        if line.active()
          result.push line.name
      return result

    @_departureTimeFilter = ko.computed =>
      from_to = @timeLimits.departureFromToTimeActive()
      from_to = from_to.split(';')
      result = {'timeFrom':from_to[0],'timeTo':from_to[1]}
      return result

    @_arrivalTimeFilter = ko.computed =>
      from_to = @timeLimits.arrivalFromToTimeActive()
      from_to = from_to.split(';')
      result = {'timeFrom':from_to[0],'timeTo':from_to[1]}
      return result

    @_departureTimeReturnFilter = ko.computed =>
      from_to = @timeLimits.departureFromToTimeReturnActive()
      from_to = from_to.split(';')
      result = {'timeFrom':from_to[0],'timeTo':from_to[1]}
      return result

    @_arrivalTimeReturnFilter = ko.computed =>
      from_to = @timeLimits.arrivalFromToTimeReturnActive()
      from_to = from_to.split(';')
      result = {'timeFrom':from_to[0],'timeTo':from_to[1]}
      return result

    @onlyDirectFilter = ko.observable 0

    @onlyShortFilter = ko.observable 0

    @serviceClassFilter = ko.observable 'A'


    @_allFilters = ko.computed =>
      return {
        'airlines': @_airlinesFilters(),
        'airports': @_airportsFilters(),
        'departureTime':@_departureTimeFilter(),
        'departureTimeReturn':@_departureTimeReturnFilter(),
        'arrivalTime':@_arrivalTimeFilter(),
        'arrivalTimeReturn':@_arrivalTimeReturnFilter(),
        'onlyDirect':@onlyDirectFilter(),
        'onlyShort': @onlyShortFilter(),
        'serviceClass': @serviceClassFilter()
      }

    @_allFilters.subscribe (value) =>
      console.log "REFILTER"
      _.each @data, (x)-> x.filter (value)
      @numResults _.reduce @data,
        (memo, result) ->
          if result.visible() then memo + 1 else memo
        , 0
      console.log @data.length

      @update_cheapest()
      ko.processAllDeferredBindingUpdates()
      # FIXME
      ResizeAvia();

    @update_cheapest()

    # Flight to show in popup
    @popup = ko.observable @cheapest()
    @done = false

  # Inject search params from response
  injectSearchParams: (sp) =>
    @arrivalCity = sp.destinations.arrival
    @departureCity = sp.destinations.departure
    @date = dateUtils.formatDayShortMonth new Date(sp.destinations.date)
        
  resetAirlines: =>
    for line in @airlines
      line.active(0)

  resetDepartureAirports: =>
    for line in @departureAirports
      line.active(0)
  resetArrivalAirports: =>
    for line in @arrivalAirports
      line.active(0)

  update_cheapest: ->
    data = _.filter @data, (el) -> el.visible()
    # FIXME hide recommend
    if data.length == 0
      return

    new_cheapest = _.reduce data,
      (el1, el2)->
        if el1.price < el2.price then el1 else el2
      data[0]
    if @cheapest() == undefined
      @cheapest new_cheapest
      return
    if @cheapest().key != new_cheapest.key
      @cheapest new_cheapest

# Model for avia search params,
# Used in AviaPanel and search controller
class SearchParams
  constructor: ->
    @dep = ko.observable 'MOW'
    @arr = ko.observable 'PAR'
    @date = '06.10.2012'
    @adults = ko.observable(1).extend({integerOnly: 'adult'})
    @children = ko.observable(0).extend({integerOnly: true})
    @infants = ko.observable(0).extend({integerOnly: 'infant'})

    @rt = ko.observable false
    @rtDate = '14.10.2012'

  url: ->
    result = 'http://api.voyanga.com/v1/flight/search/BE?'
    params = []
    params.push 'destinations[0][departure]=' + @dep()
    params.push 'destinations[0][arrival]=' + @arr()
    params.push 'destinations[0][date]=' + @date
    if @rt()
      params.push 'destinations[1][departure]=' + @arr()
      params.push 'destinations[1][arrival]=' + @dep()
      params.push 'destinations[1][date]=' + @rtDate
    params.push 'adt=' + @adults()
    params.push 'chd=' + @children()
    params.push 'inf=' + @infants()
    result += params.join "&"
    window.voyanga_debug "Generated search url", result
    return result


  key: ->
    key = @dep() + @arr() + @date
    if @rt()
      key += @rtDate
      key += '_rt'
    key += @adults()
    key += @children()
    key += @infants()
    console.log "Search key", key
    return key

  getHash: ->
    # FIXME
    parts =  [@dep(), @arr(), @date, @adults(), @children(), @infants()]
    if @rt()
      parts.push @rtDate
    hash = 'avia/search/' + parts.join('/') + '/'
    window.voyanga_debug "Generated hash for avia search", hash
    return hash


  fromList: (data)->
    # FIXME looks too ugly to hit production, yet does not support RT
    @dep data[0]
    @arr data[1]
    @date = data[2]
    @adults data[3]
    @children data[4]
    @infants data[5]
    if data.length == 7
      @rt true
      console.log "RTDATE"
      @rtDate = data[6]
