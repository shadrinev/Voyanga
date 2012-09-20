class TourEntry
  isAvia: =>
    return @avia
  isHotel: =>
    return @hotels

class ToursAviaResultSet extends TourEntry
  constructor: (raw)->
    @template = 'avia-results'
    @panel = new AviaPanel()
    @results = new AviaResultSet raw
    @results.departureCity = 'TEST'
    @results.arrivalCity = 'TEST'
    @results.date = new Date()
    @results.postInit()
    @data = {results: @results}
    @avia = true

  destinationText: =>
    @results.departureCity + ' &rarr; ' + @results.arrivalCity   
    
class ToursHotelsResultSet extends TourEntry
  constructor: (raw, @searchParams)->
    @template = 'hotels-results'
    @panel = new HotelsPanel()
    @data = new HotelsResultSet raw
    @hotels = true

  destinationText: =>
    "Отель в " + @searchParams.city

class ToursResultSet
  constructor: (raw)->
    @data = []
    for variant in raw.allVariants
      if variant.flightVoyages
        @data.push new ToursAviaResultSet variant.flightVoyages
      else
        @data.push new ToursHotelsResultSet variant.hotels, variant.searchParams

    @selected = ko.observable @data[0]
    @panel = ko.computed =>
      @selected().panel
      