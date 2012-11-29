# FIXME use mixins for most getters(?)
# TODO aviaresult.grep could be usefull

# Atomic journey unit.
class FlightPart
  constructor: (part)->
    @part = part
    @departureDate = new Date(part.datetimeBegin+'+04:00')
    @arrivalDate = new Date(part.datetimeEnd+'+04:00')
    @departureCity = part.departureCity
    @departureCityPre = part.departureCityPre
    @departureAirport = part.departureAirport
    @aircraftName = part.aircraftName
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

  departureCityStopoverText: ->
    "Пересадка в " + @departureCityPre + ", " + @stopoverText()

  # calculate stopover length to given anotherPart
  calculateStopoverLength: (anotherPart) ->
    @stopoverLength = Math.floor((anotherPart.departureDate.getTime() - @arrivalDate.getTime())/1000)

  stopoverText: -> 
    dateUtils.formatDuration @stopoverLength
    
  

class Voyage #Voyage Plus loin que la nuit et le jour = LOL)
  constructor: (flight, @airline) ->
    # Wrap flight parts in model
    @parts = []
    for part in flight.flightParts
      @parts.push new FlightPart(part)

    @flightKey = flight.flightKey
    @hasStopover = if @stopoverCount > 1 then true else false
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

  # helper function for result selection on validation/back button/events
  similarityHash: ->
    @hash() + @airline

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
    if @parts.length == 2
      part = @parts[0]
      return "Пересадка в " + part.arrivalCityPre + ", " + @parts[0].stopoverText()
    for part in @parts[0..-2]
      result.push part.arrivalCityPre
    "Пересадка в " + result.join(', ')
    
  stopoverRelText: ->
    if @direct
      return "Без пересадок"
    result = []
    for part in @parts[0..-2]
      result.push 'Пересадка в ' + part.arrivalCityPre + ', ' + part.stopoverText()
    result.join('<br />')

  stopsRatio: ->
    result = []
    if @direct
      return '<span class="down"></span>'
    duration = _.reduce @parts,
      (memo, part)-> memo + part._duration,
      0
    for part in @parts[0..-2]
      result.push {left: Math.ceil(part._duration/duration*80), part: part}
    for data, index in result
      if data.left < 18
        data.left = 18        
      if index > 0
        result[index].left = result[index-1].left+data.left
      else
        result[index].left = data.left
    htmlResult = ""
    for data in result
      htmlResult += @getCupHtmlForPart data.part, "left: " + data.left + '%'
    htmlResult += '<span class="down"></span>'

    return htmlResult

  stopoverHtml: ->
    if @direct
      return
    htmlResult = ""

    for part in @parts[0..-2]
      console.log part
      if part.stopoverLength  > 0
        htmlResult += @getCupHtmlForPart(part)

    return htmlResult

  # Returns cup html for flight part 
  getCupHtmlForPart: (part, style="")->
    cupClass = if part.stopoverLength < 2.5*60*60 then "cup" else "cupLong"
    '<span class="' + cupClass + ' tooltip" rel="Пересадка в ' + part.arrivalCityPre + ', ' + part.stopoverText() + '" style="' + style + '"></span>'

  # FIXME prolly should have cupLong here too
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
    @isFlight = true
    @isHotel = false
    # Mix in events
    _.extend @, Backbone.Events

    # for cloning result to best later
    @_data = data
    @_stacked_data = []
    flights = data.flights
    @searchId = data.searchId
    @cacheId = data.cacheId

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
    flights[0].flightKey = data.flightKey
    @activeVoyage = new Voyage(flights[0], @airline)
    if @roundTrip
      flights[1].flightKey = data.flightKey
      @activeVoyage.push new Voyage(flights[1], @airline)
    @voyages = []
    @voyages.push @activeVoyage
    @activeVoyage = ko.observable(@activeVoyage)

    @stackedMinimized = ko.observable true
    @rtStackedMinimized = ko.observable true

    @flightCodesText = if _.size(@activeVoyage().parts)>1 then "Рейсы" else "Рейс"
    @totalPeople = 0

    # Generate proxy getters
    fields = ['departureCity', 'departureAirport', 'departureDayMo', 'departureDate', 'departurePopup', 'departureTime', 'arrivalCity',
              'arrivalAirport', 'arrivalDayMo', 'arrivalDate', 'arrivalTime', 'duration', '_duration', 'direct', 'stopoverText', 'stopoverRelText', 'departureTimeNumeric',
              'arrivalTimeNumeric','hash', 'similarityHash', 'stopsRatio', 'recommendStopoverIco']

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

  rtFlightCodesText: =>
    if _.size(@activeVoyage().activeBackVoyage().parts)>1 then "Рейсы" else "Рейс"

  flightKey: =>
    if @roundTrip
      return @activeVoyage().activeBackVoyage().flightKey
    return @activeVoyage().flightKey

  flightCodes: =>
    codes = _.map @activeVoyage().parts, (flight) -> '<span class="tooltip" rel="' + flight.departureCity + ' - ' + flight.arrivalCity + '"><nobr>' + flight.flightCode + "</nobr></span>"
    Utils.implode(', ', codes)

  rtFlightCodes: =>
    codes = _.map @activeVoyage().activeBackVoyage().parts, (flight) -> '<span class="tooltip" rel="' + flight.departureCity + ' - ' + flight.arrivalCity + '"><nobr>' + flight.flightCode + "</nobr></span>"
    Utils.implode(', ', codes)

  isActive: ->
    console.log @parent.selected_key(), @key, @parent.selected_best()
    if @parent.selected_best()
      return @parent.selected_key()==@key
    @parent.selected_key()==@key
    
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
    data.flights[0].flightKey = data.flightKey
    newVoyage = new Voyage(data.flights[0], @airline)
    @_stacked_data.push data
    if @roundTrip
      data.flights[1].flightKey = data.flightKey
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
    @activeVoyage(_helper[key])
    @voyages = []
    for key, item of _helper
      if item.stopoverLength < @activeVoyage().stopoverLength
        @activeVoyage item
      @voyages.push item


  # Shows popup with detailed info about given result
  showDetails: (data, event)=>
    new GenericPopup '#avia-body-popup', @
    ko.processAllDeferredBindingUpdates()

    SizeBox('avia-body-popup');
    ResizeBox('avia-body-popup');

  chooseActive: =>
    if @visible() == false
      return
    if @activeVoyage().visible()
      return
    active = _.find @voyages, (voyage)->voyage.visible()
    if !active
      @visible(false)
      return
    @activeVoyage active

  directRating: =>
    base = 1
    if @direct()
      base += 1
    if @roundTrip
      if @rtDirect()
        base += 1
    d = @_duration() 
    if @roundTrip
      d+= @rt_duration()
    return d/base

  getParams: =>
    result = {}
    if @activeVoyage()
      result.airlineCode = @airline
      result.rt = if @roundTrip then 'true' else 'false'
      result.departureDateTime = @departureDate()
      result.arrivalDateTime = @arrivalDate()
      if @roundTrip
        result.rtDepartureDateTime = @rtDepartureDate()
        result.rtArrivalDateTime = @rtArrivalDate()

    return JSON.stringify(result)

#
# Result container
# Stacks them by price and company
#
class AviaResultSet
  constructor: (rawVoyages, @siblings=false) ->
    @recommendTemplate = 'avia-cheapest-result'
    # Indicates if we need to alter our rendering to fix tours template
    @tours = false
    @selected_key = ko.observable ''
    @selected_best = ko.observable false
    # if we want to show best flight instead of +-3 days
    @showBest = ko.observable false
    @creationMoment = moment()
    
    @_results = {}

    if !rawVoyages.length
      throw "404"

    # first pass filter interlines
    _interlines = {}
    for flightVoyage in rawVoyages
      key = ''
      for flight in flightVoyage.flights
        for part in flight.flightParts
          key += part.datetimeBegin
          key += part.datetimeEnd
       if _interlines[key]
        if _interlines[key].price > flightVoyage.price
          _interlines[key] = flightVoyage
       else
          _interlines[key] = flightVoyage

    filteredVoyages = []
    for key, item of _interlines
      filteredVoyages.push item
    

    for flightVoyage in filteredVoyages
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
    @filtersConfig = false

    for key, result of @_results
      result.sort()
      result.removeSimilar()
      @data.push result

    @data.sort (left, right)=>
      left.price - right.price

    @postFilters()

    
  # Inject search params from response
  injectSearchParams: (sp) =>
    @rawSP = sp
    @arrivalCity = sp.destinations[0].arrival
    @departureCity = sp.destinations[0].departure
    @rawDate = moment(new Date(sp.destinations[0].date+'+04:00'))
    @date = dateUtils.formatDayShortMonth new Date(sp.destinations[0].date+'+04:00')
    @dateHeadingText = @date
    @roundTrip = sp.isRoundTrip
    if @roundTrip
      @rtDate = dateUtils.formatDayShortMonth new Date(sp.destinations[1].date+'+04:00')
      @rawRtDate = moment(new Date(sp.destinations[1].date+'+04:00'))

      @dateHeadingText += ', ' +@rtDate      

  select: (ctx) =>
    console.log ctx
    # cheapest click
    if ctx.ribbon
      selection = ctx.data
    else
      selection = ctx

    ticketValidCheck = $.Deferred()
    ticketValidCheck.done (selection)->
      result = {}
      result.module = 'Avia'
      result.type = 'avia'
      result.searchId = selection.cacheId
      # FIXME FIXME FXIME
      result.searchKey = selection.flightKey()
      Utils.toBuySubmit [result]
      
    @checkTicket selection, ticketValidCheck

  findAndSelect: (result)=>
    hash = result.similarityHash()
    for result in @data
      for voyage in result.voyages
        if voyage.similarityHash()==hash
          result.activeVoyage voyage
          if !@roundTrip
            return result
          backHash = voyage.activeBackVoyage().similarityHash()
          for backVoyage in voyage._backVoyages
            if backVoyage.similarityHash() == backHash
              voyage.activeBackVoyage backVoyage
              return result
    return false
          

  postInit: =>
    @filters = new AviaFiltersT @
    @filters.serviceClass.selection.subscribe (newValue)=>
      if newValue == 'B'
        @showBest true
        return
      @showBest false
    if @siblings
      eCheapest = _.reduce @data,
        (el1, el2)->
          if el1.price < el2.price then el1 else el2
        ,@data[0]
      data = _.filter @data, (item)-> item.serviceClass=='B'
      bCheapest = _.reduce data,
        (el1, el2)->
          if el1.price < el2.price then el1 else el2
        ,data[0]
      if !eCheapest
        eCheapest = {price: 0} 
      if !bCheapest
        bCheapest = {price: 0} 

      @ESiblings = @processSiblings @siblings.E, eCheapest
#      @BSiblings = @processSiblings @siblings.B, bCheapest
      @siblings = ko.observable @ESiblings

  processSiblings: (rawSiblings, cheapest)=>
    helper = (root, sibs, today=false) =>
      for price,index in sibs
        root[index] = {price: price, siblings:[]}

    if @roundTrip
      rawSiblings[3][3] = Math.ceil(cheapest.price/2)
    else
      rawSiblings[3] = cheapest.price
      
    if rawSiblings[3].length
      siblings = []
      todayPrices = []
      for sibs, index in rawSiblings
        sibs = _.filter sibs, (item)->item!=false
        if sibs.length
          min = _.min sibs
        else
          min = false
        todayPrices[index] = min
      helper(siblings, todayPrices,true)
      for sibs,index in rawSiblings
        helper(siblings[index].siblings, sibs)
    else
      siblings = []
      helper(siblings, rawSiblings,true)
    return new Siblings(siblings, @roundTrip,  @rawDate, @rawRtDate)    

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
    jsPaneScrollHeight()
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
          
  setBest: (oldresult, unconditional=false)=>
    # FIXME could leak as hell
    key = oldresult.key
    result = new AviaResult oldresult._data, @
    for item in oldresult._stacked_data
      result.push item
    result.sort()
    result.removeSimilar()
    result.best = true
    result.key = key + '_optima'

    if !unconditional
      result.voyages = _.filter result.voyages, (el)->el.maxStopoverLength <60*60*3
      _.each result.voyages, (voyage)->
    #    voyage.activeBackVoyage = ko.observable voyage.activeBackVoyage()
        voyage._backVoyages = _.filter voyage._backVoyages, (el)-> el.maxStopoverLength <60*60*3
    result.chooseStacked oldresult.activeVoyage()
    
    if @best() == undefined
      @best result
      return
    if @best().key != result.key
      delete @best()
      @best result

  filtersRendered: ->
    ko.processAllDeferredBindingUpdates()

# Model for avia search params,
# Used in AviaPanel and search controller
class AviaSearchParams extends SearchParams
  constructor: ->
    super
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