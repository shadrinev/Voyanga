MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентрября', 'октября','ноября', 'декабря']

class Voyage #Voyage Plus loin que la nuit et le jour
  constructor: (flight) ->
    @parts = flight.flightParts
    @direct = @parts.length == 1

    # FIXME is this  utc?
    @departureDate = new Date(flight.departureDate)
    @arrivalDate = new Date(@parts[@parts.length-1].datetimeEnd)

    @_duration = flight.fullDuration

    @departureAirport = @parts[0].departureAirport
    @arrivalAirport = @parts[@parts.length-1].arrivalAirport

    @departureCity = flight.departureCity
    @arrivalCity = flight.arrivalCity

    @_backVoyages = []
    @activeBackVoyage = ko.observable()

  # returns our sort key
  departureInt: ->
    @departureDate.getHours()*60+@departureDate.getMinutes()

  # Helper function, returns hash to check equality for first flight
  hash: ->
    @departureTime() + @arrivalTime()

  # pushes available back flight variants
  push: (voyage)->
    @_backVoyages.push voyage

  stacked: ->
    @_backVoyages.length > 1

  departureDayMo: ->
    result = ""
    result+= @departureDate.getDate()
    result+= " "
    result+= MONTHS[@departureDate.getMonth()]

  departureTime: ->
    result = ""
    result+= @departureDate.getHours()
    result+=":"
    minutes = @departureDate.getMinutes().toString()
    if minutes.length == 1
      minutes = "0" + minutes
    result+= minutes

  arrivalDayMo: ->
    result = ""
    result+= @arrivalDate.getDate()
    result+= " "
    result+= MONTHS[@arrivalDate.getMonth()]

  arrivalTime: ->
    result = ""
    result+= @arrivalDate.getHours()
    result+=":"
    minutes = @arrivalDate.getMinutes().toString()
    if minutes.length == 1
      minutes = "0" + minutes
    result+= minutes


  fullDuration: ->
    # LOL!
    all_minutes = @_duration / 60
    minutes = all_minutes % 60
    hours = (all_minutes - minutes) / 60
    hours + " ч. " + minutes + " м."


  stopoverText: ->
    result = []
    for part in @parts[0..-2]
      result.push part.arrivalCity
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
    @_backVoyages.sort((a,b) -> a.departureInt() - b.departureInt())
    @activeBackVoyage(@_backVoyages[0])


#
# Coomon parts of StackedVoyage
#
class Result
  constructor: (data) ->
    flights = data.flights
    #! FIXME should magically work w/o ceil
    @price = Math.ceil(data.price)
    @_stacked = false
    @roundTrip = flights.length == 2
    @visible = ko.observable true

    @airline = data.valCompany

    @activeVoyage = new Voyage(flights[0])
    if @roundTrip
      @activeVoyage.push new Voyage(flights[1])
    @voyages = []
    @voyages.push @activeVoyage
    @activeVoyage = ko.observable(@activeVoyage)

    # Generate proxy getters
    fields = ['departureCity', 'departureAirport', 'departureDayMo', 'departureTime', 'arrivalCity',
              'arrivalAirport', 'arrivalDayMo', 'arrivalTime', 'fullDuration', 'direct', 'stopoverText',
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
      found = false
      fields = ['departureAirport', 'arrivalAirport']
      if @roundTrip
        fields.push 'rtDepartureAirport'
        fields.push 'rtArrivalAirport'
      for field in fields
        if filters.airports.indexOf(@[field]()) >= 0
          found = true
      match_ports = found
      if filters.airports.length == 0
        match_ports = true

      found = false
      if filters.airlines.indexOf(@airline) >= 0
        found = true
      match_lines = found
      if filters.airlines.length == 0
        match_lines = true

      @visible(match_ports&&match_lines)

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
    @activeVoyage(voyage)

  chooseRtStacked: (voyage) =>
    @activeVoyage().activeBackVoyage(voyage)

  rtVoyages: ->
      @activeVoyage()._backVoyages

  sort: ->
    @voyages.sort((a,b) -> a.departureInt() - b.departureInt())
    if @roundTrip
      _.each(@voyages, (x)-> x.sort() )
    @activeVoyage(@voyages[0])
#
# Result container
# Stacks them by price and company 
#
class ResultSet
  constructor: (rawVoyages) ->
    @_results = {}

    for flightVoyage in rawVoyages
      key = flightVoyage.price + "_" + flightVoyage.valCompany
      if @_results[key]
        @_results[key].push flightVoyage
      else
        @_results[key] = new Result flightVoyage

    @cheapest = ko.observable()
    # We need array for knockout to work right
    @data = []

    @airports = []
    @airlines = []

    # Temporary
    _airports = {}
    _airlines = {}

    for key, result of @_results
      @data.push result
      _airlines[result.airline] = 1
      _airports[result.departureAirport()]=1
      _airports[result.arrivalAirport()]=1
      if result.roudTrip
        _airports[result.rtDepartureAirport()]=1
        _airports[result.rtArrivalAirport()]=1

    for key, foo of _airports
      @airports.push {'name':key, 'active': ko.observable 0 }

    for key, foo of _airlines
      @airlines.push {'name':key, 'active': ko.observable 0 }

    @_airportsFilters = ko.computed =>
          result = []
          for port in @airports
            if port.active()
              result.push port.name
          return result

    @_airlinesFilters = ko.computed =>
      result = []
      for line in @airlines
        if line.active()
          result.push line.name
      return result


    @_allFilters = ko.computed =>
      return {'airlines': @_airlinesFilters(), 'airports': @_airportsFilters()}

    @_allFilters.subscribe (value) =>
      _.each @data, (x)-> x.filter (value)
      @update_cheapest()

    for result in @data
      result.sort()

    @update_cheapest()

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
    widthHowLong()

$ ->
  stacked = new ResultSet data.flights.flightVoyages
  ko.applyBindings({'results': stacked})
