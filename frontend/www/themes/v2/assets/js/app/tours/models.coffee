class TourEntry
  isAvia: =>
    return @avia
  isHotel: =>
    return @hotels
  price: =>
    @selection().price

class ToursAviaResultSet extends TourEntry
  constructor: (raw, @searchParams)->
    @template = 'avia-results'
    @panel = new AviaPanel()
    @results = new AviaResultSet raw
    @results.injectSearchParams @searchParams
    @results.postInit()
    @data = {results: @results}
    @avia = true
    @selection = ko.observable @results.data[0]

  destinationText: =>
    @results.departureCity + ' &rarr; ' + @results.arrivalCity   
    
class ToursHotelsResultSet extends TourEntry
  constructor: (raw, @searchParams)->
    
    @template = 'hotels-results'
    @panel = new HotelsPanel()
    @results = new HotelsResultSet raw
    @data = {results: @results}
    @hotels = true
    @selection = ko.observable @results.data[0].roomSets[0]

  destinationText: =>
    "Отель в " + @searchParams.city

class ToursResultSet
  constructor: (raw)->
    @data = []
    for variant in raw.allVariants
      if variant.flights
        @data.push new ToursAviaResultSet variant.flights.flightVoyages, variant.searchParams
      else
        @data.push new ToursHotelsResultSet variant.hotels, variant.searchParams

    @selected = ko.observable @data[0]
    @panel = ko.computed =>
      @selected().panel

    @price = ko.computed =>
      sum = 0
      for item in @data
        sum += item.price()
      return sum