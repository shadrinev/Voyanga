class TourEntry
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

    
  rt: =>
    false

class ToursAviaResultSet extends TourEntry
  constructor: (raw, @searchParams)->
    @template = 'avia-results'
    # FIXME
    #new Searchparams...
    @panel = new AviaPanel()
    @results = new AviaResultSet raw
    @results.injectSearchParams @searchParams
    @results.postInit()
    @results.recommendTemplate = 'avia-tours-recommend'
    @results.tours = true
    @results.select = (result)=>
      # FIXME looks retardely stupid
      if result.ribbon
        #it is actually recommnd ticket
        result = result.data
      @results.selected_key result.key
      console.log result.key
      @selection(result)
    @data = {results: @results}
    @avia = true
    @selection = ko.observable null

  destinationText: =>
    @results.departureCity + ' &rarr; ' + @results.arrivalCity   

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
      source = @results.data[0]
    result = '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth source.departureDate()
    result+='</div>'
    if @rt()
      result+= '<div class="day">'
      result+= dateUtils.formatHtmlDayShortMonth source.rtDepartureDate()
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
    @selection = ko.observable null
    
  destinationText: =>
    "Отель в " + @searchParams.city

  price: =>
    if @selection() == null
      return 0

    @selection().roomSets[0].price

  additionalText: =>
    if @selection() == null
      return ""
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