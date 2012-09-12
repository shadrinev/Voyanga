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
    @transportAirlineName = part.transportAirlineName
    @flightCode = part.transportAirline + ' ' + part.flightCode

  departureTime: ->
    dateUtils.formatTime @departureDate

  arrivalTime: ->
    dateUtils.formatTime @arrivalDate

  duration: ->
    dateUtils.formatDuration @_duration

class Voyage #Voyage Plus loin que la nuit et le jour
  constructor: (flight) ->
    # Wrap flight parts in model
    @parts = []
    for part in flight.flightParts
      @parts.push new FlightPart(part)

    @direct = @parts.length == 1

    @serviceClass = 'E'

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
    @_backVoyages.length > 1

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
    result = []
    for part in @parts[0..-2]
      result.push part.arrivalCityPre
    result.join(', ')

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
    return result

  sort: ->
    #console.log "SORTENG "
    @_backVoyages.sort((a,b) -> a.departureInt() - b.departureInt())
    @activeBackVoyage(@_backVoyages[0])

  filter: (filters)->
    result = true
    match_departure_time = false
    if filters.departureTime.timeFrom <= @departureTimeNumeric() && filters.departureTime.timeTo >= @departureTimeNumeric()
      match_departure_time = true
    result = result && match_departure_time

    match_arrival_time = false
    if filters.arrivalTime.timeFrom <= @arrivalTimeNumeric() && filters.arrivalTime.timeTo >= @arrivalTimeNumeric()
      match_arrival_time = true
    result = result && match_arrival_time

    if filters.onlyDirect == '1'
      result = result && @direct

    if filters.serviceClass != 'A'
      result = result && @serviceClass == filters.serviceClass
    haveBack = false
    for rtVoyage in @_backVoyages
      thisBack = true
      match_departure_time = false
      if filters.departureTimeReturn.timeFrom <= rtVoyage.departureTimeNumeric() && filters.departureTimeReturn.timeTo >= rtVoyage.departureTimeNumeric()
        match_departure_time = true
      thisBack = result && match_departure_time

      if filters.onlyDirect == '1'
        thisBack = thisBack && rtVoyage.direct

      match_arrival_time = false
      if filters.arrivalTimeReturn.timeFrom <= rtVoyage.arrivalTimeNumeric() && filters.arrivalTimeReturn.timeTo >= rtVoyage.arrivalTimeNumeric()
        match_arrival_time = true
      thisBack = thisBack && match_arrival_time
      rtVoyage.visible(thisBack)
      if thisBack && !haveBack
        haveBack = true
        @activeBackVoyage(rtVoyage)

    result = result && haveBack
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
    @airlineName = data.valCompanyName

    @activeVoyage = new Voyage(flights[0])
    if @roundTrip
      @activeVoyage.push new Voyage(flights[1])
    @voyages = []
    @voyages.push @activeVoyage
    @activeVoyage = ko.observable(@activeVoyage)


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
      if filters.airports.length == 0
        match_ports = true
      else
        match_ports = false
        fields = ['departureAirport', 'arrivalAirport']
        if @roundTrip
          fields.push 'rtDepartureAirport'
          fields.push 'rtArrivalAirport'
        for field in fields
          if filters.airports.indexOf(@[field]()) >= 0
            match_ports = true
            break



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



      @visible(match_ports&&match_lines&&some_visible)

  stacked: ->
    @_stacked

  rtStacked: ->
    @activeVoyage().stacked()

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
    @activeVoyage(voyage)

  chooseRtStacked: (voyage) =>
    window.voyanga_debug "Choosing RT stacked voyage", voyage
    @activeVoyage().activeBackVoyage(voyage)

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
        result.on "popup", (data)=>
          @popup data

    @cheapest = ko.observable()
    # We need array for knockout to work right
    @data = []

    @airports = []
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
    _airlines = {}

    for key, result of @_results
      result.sort()
      @data.push result
      _airlines[result.airline] = result.airlineName
      _airports[result.departureAirport()]=1
      _airports[result.arrivalAirport()]=1
      #console.log(result.departureTimeNumeric())
      if result.roundTrip
        _airports[result.rtDepartureAirport()]=1
        _airports[result.rtArrivalAirport()]=1
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



    for key, foo of _airports
      @airports.push {'name':key, 'active': ko.observable 0 }

    for key, foo of _airlines
      @airlines.push {'name':key,'visibleName':foo, 'active': ko.observable 0 }

    @_airportsFilters = ko.computed =>
          result = []
          for port in @airports
            if port.active()
              result.push port.name
          return result

    @_airlinesFilters = ko.computed =>
      result = []
      console.log 'airlines'
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
      console.log('refilter');
      _.each @data, (x)-> x.filter (value)
      @update_cheapest()

    for result in @data
      result.sort()

    @update_cheapest()

    # Flight to show in popup
    @popup = ko.observable @cheapest()

  update_cheapest: ->
    # FIXME 0 items
    cheapest_reseted = false
    for key, result of @_results
      if result.visible()
        if !cheapest_reseted
          @cheapest(result)
          cheapest_reseted = true
          continue
        if result.price < @cheapest().price
          @cheapest(result)

# Model for avia search params,
# Used in AviaPanel and search controller
class SearchParams
  constructor: ->
    @dep = ko.observable 'MOW'
    @arr = ko.observable 'PAR'
    @date = '02.10.2012'
    @adults = ko.observable(5).extend({integerOnly: 'adult'})
    @children = ko.observable(2).extend({integerOnly: true})
    @infants = ko.observable(2).extend({integerOnly: 'infant'})

    @rt = ko.observable false
    @rt_date = '12.10.2012'

  url: ->
    result = 'http://api.voyanga/v1/flight/search/withParams?'
    params = []
    params.push 'destinations[0][departure]=' + @dep()
    params.push 'destinations[0][arrival]=' + @arr()
    params.push 'destinations[0][date]=' + @date
    if @rt()
      params.push 'destinations[1][departure]=' + @arr()
      params.push 'destinations[1][arrival]=' + @dep()
      params.push 'destinations[1][date]=' + @rt_date
    params.push 'adt=' + @adults()
    params.push 'chd=' + @children()
    params.push 'inf=' + @infants()
    result += params.join "&"
    window.voyanga_debug "Generated search url", result
    return result


  key: ->
    key = @dep() + @arr() + @date
    if @rt
      key += @rt_date
    key += @adults()
    key += @children()
    key += @infants()
    return key

  getHash: ->
    # FIXME
    hash = 'avia/search/' + [@dep(), @arr(), @date, @adults(), @children(), @infants()].join('/') + '/'
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
