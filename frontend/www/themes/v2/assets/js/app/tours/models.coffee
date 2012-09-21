class TourEntry
  isAvia: =>
    return @avia
  isHotel: =>
    return @hotels
  price: =>
    @selection().price
  rt: =>
    false

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

  additionalText: =>
    if @rt()
      ""
    else
      ", " + @selection().departureTime() + ' - ' + @selection().arrivalTime()

  dateClass: =>
    if @rt() then 'blue-two' else 'blue-one'

  dateHtml: =>
    result = '<div class="day">'
    # FIXME use @results
    result+= dateUtils.formatHtmlDayShortMonth @selection().departureDate()
    result+='</div>'
    if @rt()
      result+= '<div class="day">'
      result+= dateUtils.formatHtmlDayShortMonth @selection().rtDepartureDate()
      result+= '</div>'
    return result

    
  rt: =>
    @results.roundTrip
       
class ToursHotelsResultSet extends TourEntry
  constructor: (raw, @searchParams)->
    
    @template = 'hotels-results'
    @panel = new HotelsPanel()
    @results = new HotelsResultSet raw, @searchParams
    @data = {results: @results}
    @hotels = true
    @selection = ko.observable @results.data[0]
    
  destinationText: =>
    "Отель в " + @searchParams.city

  price: =>
    @selection().roomSets[0].price

  additionalText: =>
    ", " + @selection().hotelName

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
    @data = []
    for variant in raw.allVariants
      if variant.flights
        @data.push new ToursAviaResultSet variant.flights.flightVoyages, variant.searchParams
      else
        @data.push new ToursHotelsResultSet variant.hotels, variant.searchParams

    @selection = ko.observable @data[0]
    @panel = ko.computed =>
      @selection().panel

    @price = ko.computed =>
      sum = 0
      for item in @data
        sum += item.price()
      return sum

  setActive: (entry)=>
    @selection entry
    ko.processAllDeferredBindingUpdates()
    ResizeAvia()