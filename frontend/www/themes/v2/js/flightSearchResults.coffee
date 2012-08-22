MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентрября', 'ноября', 'декабря']

class Voyage #Voyage Plus loin que la nuit et le jour
  constructor: (data, result) ->
    @parts = []

    flights = data.flights
    # FIXME is this  utc?
    @departureDate = new Date(flights[0].departureDate)
    # FIXME move it to flight at serverside
    @parts = flights[0].flightParts
    @arrivalDate = new Date(@parts[@parts.length-1].datetimeEnd)
    @_duration = flights[0].fullDuration


  # returns pur sort key
  departureInt: ->
    @departureDate.getHours()*60+@departureDate.getMinutes()

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
    @departureCity = data.flights[0].departureCity

    if !@roundTrip
      # TODO We CAN == SHOULD get this from search params (?)
      @arrivalCity = data.flights[0].arrivalCity
      @parts = flights[0].flightParts
      @direct = @parts.length == 1
      @departureAirport = @parts[0].departureAirport
      @arrivalAirport = @parts[@parts.length-1].arrivalAirport

    @activeVoyage = new Voyage(data, @)
    @voyages = []
    @voyages.push @activeVoyage
    @activeVoyage = ko.observable(@activeVoyage)


  ########################
  # Proxy date call to active voyage
  ########################
  departureDayMo: ->
    @activeVoyage().departureDayMo()

  departureTime: ->
    @activeVoyage().departureTime()

  arrivalDayMo: ->
    @activeVoyage().arrivalDayMo()

  arrivalTime: ->
    @activeVoyage().arrivalTime()


  fullDuration: ->
    @activeVoyage().fullDuration()

  ##########################
  # Non direct flight stuff
  ##########################
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

  stopoverText: ->
    result = []
    # FIXME: check if this is actually works
    for part in @parts[0..-2]
      result.push part.arrivalCity
    result.join(', ')

  stacked: ->
    @_stacked

  push: (data) ->
    @_stacked = true
    @voyages.push new Voyage(data)

  chooseStacked: (voyage) =>
    @activeVoyage(voyage)
  sort: ->
    @voyages.sort((a,b) -> a.departureInt() - b.departureInt())
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
    # FIXME 0 items
    @cheapest = @_results[key]
    for key, result of @_results
      if result.price < @cheapest.price
        @cheapest = result

    # We need array for knockout to work right
    @data = []
    for key, result of @_results
      @data.push result

    for result in @data
      result.sort()


$ ->
  console.log data
  stacked = new ResultSet data.flights.flightVoyages
  ko.applyBindings({'results': stacked})