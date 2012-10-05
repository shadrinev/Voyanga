# FIXME use mixins for most getters(?)

# Atomic journey unit.
class FlightPart
  constructor: (part)->
    @departureDate = new Date(part.datetimeBegin+'+04:00')
    @arrivalDate = new Date(part.datetimeEnd+'+04:00')
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
  constructor: (flight, @airline) ->
    # Wrap flight parts in model
    @parts = []
    for part in flight.flightParts
      @parts.push new FlightPart(part)

    @stopoverLength = 0
    @maxStopoverLength = 0 
    @direct = @parts.length == 1
    if ! @direct
      for part, index in @parts
        if index < (@parts.length - 1)
          part.calculateStopoverLength @parts[index+1]
        @stopoverLength += part.stopoverLength
        if part.stopoverLength > @maxStopoverLength
          @maxStopoverLength = part.stopoverLength

    # FIXME !!!!!!!!!!!!!!!!!!!!!
    @departureDate = new Date(flight.departureDate+'+04:00')
    # fime it is converted already
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
      return '<span class="down"></span>'
    duration = _.reduce @parts,
      (memo, part)-> memo + part._duration,
      0
    for part in @parts[0..-2]
      result.push Math.ceil part._duration/duration*80
    for data, index in result
      if data < 18
        data = 18        
      if index > 0
        result[index] = result[index-1]+data
      else
        result[index] = data
    htmlResult = ""
    for left in result
      htmlResult += '<span class="cup" style="left: ' + left + '%;"></span>'
    htmlResult += '<span class="down"></span>'

    return htmlResult

  recommendStopoverIco: ->
    if @direct
      return
    '<span class="cup"></span>'

  sort: ->
    #console.log "SORTENG "
    @_backVoyages.sort((a,b) -> a.departureInt() - b.departureInt())
    @activeBackVoyage(@_backVoyages[0])

  # FIXME copypaste
  removeSimilar: ->
    if @_backVoyages.length < 2
      return
    _helper = {}
    for voyage in @_backVoyages
      key = voyage.airline + voyage.departureInt()
      item = _helper[key]
      if item
        _helper[key] = if item.stopoverLength < voyage.stopoverLength then item else voyage
      else
        _helper[key] = voyage
    @_backVoyages = []
    for key, item of _helper
      @_backVoyages.push item
    @activeBackVoyage(@_backVoyages[0])


  chooseActive: ->
    if @_backVoyages.length == 0
      return
    if @activeBackVoyage().visible()
      return
    active = _.find @_backVoyages, (voyage)->voyage.visible()
    if !active
      @visible(false)
      return
    @activeBackVoyage active



#
# Coomon parts of StackedVoyage
#
class AviaResult
  constructor: (data, @parent) ->
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
    @refundable = data.refundable
    @refundableText = if @refundable then "Билет возвратный" else "Билет не возвратный"
    @freeWeight = data.freeWeight
    @freeWeight = '$' if @freeWeight == '0'
    @freeWeightText = data.freeWeightDescription

    @activeVoyage = new Voyage(flights[0], @airline)
    if @roundTrip
      @activeVoyage.push new Voyage(flights[1], @airline)
    @voyages = []
    @voyages.push @activeVoyage
    @activeVoyage = ko.observable(@activeVoyage)

    @stackedMinimized = ko.observable true
    @rtStackedMinimized = ko.observable true


    # Generate proxy getters
    fields = ['departureCity', 'departureAirport', 'departureDayMo', 'departureDate', 'departurePopup', 'departureTime', 'arrivalCity',
              'arrivalAirport', 'arrivalDayMo', 'arrivalTime', 'duration', 'direct', 'stopoverText', 'departureTimeNumeric', 'arrivalTimeNumeric',
              'hash', 'stopsRatio', 'recommendStopoverIco']

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

  stacked: ->
    count = 0
    for voyage in @voyages
      if voyage.visible()
        count++;
      if count > 1
        return true
    return false

  rtStacked: ->
    count = 0
    for voyage in @activeVoyage()._backVoyages
      if voyage.visible()
        count++;
      if count > 1
        return true
    return false

  push: (data) ->
    @_stacked = true
    newVoyage = new Voyage(data.flights[0], @airline)
    if @roundTrip
      backVoyage = new Voyage(data.flights[1], @airline)
      newVoyage.push(backVoyage)
      result = _.find @voyages, (voyage) -> voyage.hash()==newVoyage.hash()
      if result
        result.push(backVoyage)
        return
    @voyages.push newVoyage

  chooseStacked: (voyage) =>
    window.voyanga_debug "Choosing stacked voyage", voyage
    if @roundTrip
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
      _.each @voyages,
       (x)->
        x.sort()
        x.removeSimilar() 
    @activeVoyage(@voyages[0])

  removeSimilar: ->
    if @voyages.length < 2
      return
    _helper = {}
    for voyage in @voyages
      key = voyage.airline + voyage.departureInt()
      item = _helper[key]
      if item
        _helper[key] = if item.stopoverLength < voyage.stopoverLength then item else voyage
      else
        _helper[key] = voyage
    @voyages = []
    for key, item of _helper
      @voyages.push item
    @activeVoyage(@voyages[0])

  # Shows popup with detailed info about given result
  showDetails: (data, event)=>
    new GenericPopup '#avia-body-popup', ko.contextFor(event.currentTarget)
    ko.processAllDeferredBindingUpdates()

    SizeBox('avia-body-popup');
    ResizeBox('avia-body-popup');

  chooseActive: ->
    if @visible() == false
      return
    if @activeVoyage().visible()
      return
    active = _.find @voyages, (voyage)->voyage.visible()
    if !active
      @visible(false)
      return
    @activeVoyage active

#
# Result container
# Stacks them by price and company
#
class AviaResultSet
  constructor: (rawVoyages) ->
    @recommendTemplate = 'avia-cheapest-result'
    # Indicates if we need to alter our rendering to fix tours template
    @tours = false
    @selected_key = ko.observable ''
    
    @_results = {}

    for flightVoyage in rawVoyages
      key = flightVoyage.price + "_" + flightVoyage.valCompany
      if @_results[key]
        @_results[key].push flightVoyage
      else
        result =  new AviaResult flightVoyage, @
        @_results[key] = result
        result.key = key
    # specials
    @cheapest = ko.observable()
    @best = ko.observable()
    # We need array for knockout to work right
    @data = []

    @numResults = ko.observable 0


    for key, result of @_results
      result.sort()
      result.removeSimilar()
      @data.push result

    @postFilters()
    
  # Inject search params from response
  injectSearchParams: (sp) =>
    @arrivalCity = sp.destinations[0].arrival
    @departureCity = sp.destinations[0].departure
    @date = dateUtils.formatDayShortMonth new Date(sp.destinations[0].date+'+04:00')
    @dateHeadingText = @date
    @roundTrip = sp.isRoundTrip
    if @roundTrip
      @rtDate = dateUtils.formatDayShortMonth new Date(sp.destinations[1].date+'+04:00')
      @dateHeadingText += ', ' +@rtDate
  

  select: (el) =>


  postInit: =>
    @filters = new AviaFiltersT @


  hideRecommend: (context, event)->
   hideRecomendedBlockTicket.apply(event.currentTarget)
  
  postFilters: =>
    data = _.filter @data, (el) -> el.visible()
    @numResults data.length
    # FIXME hide recommend
    @updateCheapest(data)
    @updateBest(data)

    ko.processAllDeferredBindingUpdates()
    # FIXME
    ResizeAvia()

  updateCheapest: (data)=>
    if data.length == 0
      return
 
    new_cheapest = _.reduce data,
      (el1, el2)->
        if el1.price < el2.price then el1 else el2
      ,data[0]
    if @cheapest() == undefined
      @cheapest new_cheapest
      return
    if @cheapest().key != new_cheapest.key
      @cheapest new_cheapest

  updateBest: (data)=>
    if data.length == 0
      return
 
    data = _.sortBy data, (el)-> el.price
    for result in data
      # Choose fastest first
      voyages = _.sortBy result.voyages, (el) -> el._duration
      for voyage in voyages
        if voyage.visible() && voyage.maxStopoverLength < 60*60*3
          if result.roundTrip
            # Choose fastest first
            backVoyages = _.sortBy voyage._backVoyages, (el) -> el._duration
            for backVoyage in backVoyages
              if backVoyage.visible() && backVoyage.maxStopoverLength < 60*60*3
                voyage.activeBackVoyage backVoyage
                result.activeVoyage voyage
                @setBest result
                return
          else
            result.activeVoyage voyage
            @setBest result
            return
    @setBest data[0], true
          
  setBest: (result, unconditional=false)=>
    # FIXME could leak as hell
    result = _.clone result
    result.activeVoyage = ko.observable result.activeVoyage()

    if !unconditional
      result.key = result.key + '_optima'
      result.voyages = _.filter result.voyages, (el)->el.maxStopoverLength <60*60*3
      _.each result.voyages, (voyage)->
    #    voyage.activeBackVoyage = ko.observable voyage.activeBackVoyage()
        voyage._backVoyages = _.filter voyage._backVoyages, (el)->el.maxStopoverLength <60*60*3

    if @best() == undefined
      @best result
      return
    if @best().key != result.key
      delete @best()
      @best result

  filtersRendered: ->
    ko.processAllDeferredBindingUpdates()
    scrollValue()
    $('.scrollBlock').scroll ->
  		scrollValue()
    setTimeout scrolShowFilter, 1000;

# Model for avia search params,
# Used in AviaPanel and search controller
class AviaSearchParams extends SearchParams
  constructor: ->
    super()
    @dep = ko.observable ''
    @arr = ko.observable ''
    @rt = ko.observable true
    @rtDate = ko.observable ''
    @passengers = new Passengers()
    @adults = @passengers.adults
    @children = @passengers.children
    @infants = @passengers.infants

  url: ->
    result = 'flight/search/BE?'
    params = []
    params.push 'destinations[0][departure]=' + @dep()
    params.push 'destinations[0][arrival]=' + @arr()
    params.push 'destinations[0][date]=' + moment(@date()).format('D.M.YYYY')
    if @rt()
      params.push 'destinations[1][departure]=' + @arr()
      params.push 'destinations[1][arrival]=' + @dep()
      params.push 'destinations[1][date]=' + moment(@rtDate()).format('D.M.YYYY')

    params.push 'adt=' + @adults()
    params.push 'chd=' + @children()
    params.push 'inf=' + @infants()
    result += params.join "&"
    window.voyanga_debug "Generated search url", result
    return result

  key: ->
    key = @dep() + @arr() + @date()
    if @rt()
      key += @rtDate()
      key += '_rt'
    key += @adults()
    key += @children()
    key += @infants()
    return key

  getHash: ->
    parts =  [@dep(), @arr(), moment(@date()).format('D.M.YYYY'), @adults(), @children(), @infants()]
    if @rt()
      parts.push moment(@rtDate()).format('D.M.YYYY')
    hash = 'avia/search/' + parts.join('/') + '/'
    window.voyanga_debug "Generated hash for avia search", hash
    return hash


  fromList: (data)->
    # FIXME looks too ugly to hit production, yet does not support RT
    @dep data[0]
    @arr data[1]
    @date moment(data[2], 'D.M.YYYY').toDate()
    @adults data[3]
    @children data[4]
    @infants data[5]
    if data.length == 7
      @rt true
      @rtDate  moment(data[6], 'D.M.YYYY').toDate()
    else
      @rt false

  fromObject: (data)->
    console.log data
    @adults data.adt
    @children data.chd
    @infants data.inf
    @rt data.isRoundTrip
    @dep data.destinations[0].departure_iata
    @arr data.destinations[0].arrival_iata
    # FIXME dates are fuckd
    @date new Date(data.destinations[0].date)
    if @rt()
      @rtDate new Date(data.destinations[1].date)