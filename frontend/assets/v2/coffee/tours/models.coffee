class TourEntry
  constructor: ->
    # Mix in events
    _.extend @, Backbone.Events
    @savingsWithAviaOnly = false

  isAvia: =>
    return @avia

  isHotel: =>
    return @hotels

  price: =>
    if @selection() == null
      return 0
    @selection().price

  priceHtml: =>
    if @noresults
      return "Нет результатов"
    if @selection() == null
      return "Не выбрано"
    return Utils.formatPrice(@price()) + '<span class="rur">o</span>'


  minPriceHtml: =>
    Utils.formatPrice(@minPrice()) + '<span class="rur">o</span>'

  maxPriceHtml: =>
    Utils.formatPrice(@maxPrice()) + '<span class="rur">o</span>'

  savings: =>
    if @selection() == null
      return 0
    return 0

  rt: =>
    false

  beforeRender: =>

class ToursAviaResultSet extends TourEntry
  constructor: (raw, sp)->
    super
    @api = new AviaAPI
    @template = 'avia-results'
    @overviewTemplate = 'tours-overview-avia-no-selection'
    @panel = new AviaPanel()
    @panel.handlePanelSubmit = @doNewSearch
    @panel.sp.fromObject sp
    @panel.original_template = @panel.template
    @results = ko.observable()
    @selection = ko.observable null
    @observableSP = ko.observable null
    @newResults raw, sp
    @data = {results: @results}

  newResults: (raw, sp)=>
    @rawSP = sp
    @observableSP sp
    result = new AviaResultSet raw
    result.injectSearchParams sp
    result.postInit()
    result.recommendTemplate = 'avia-tours-recommend'
    result.tours = true
    result.select = (res)=>
      @select res, result
      @trigger 'next'
    @avia = true
    @noresults = result.noresults
    @results result

  GAKey: =>
    @rawSP.destinations[0].departure_iata + '/' + @rawSP.destinations[0].arrival_iata

  GAData: =>
    result = ''
    if @rt()
      result += '2'
    else
      result += '1'
    passangers = [@rawSP.adt, @rawSP.chd, @rawSP.inf]
    result +=', ' + passangers.join(" - ")
    dest = @rawSP.destinations[0]
    if @rt()
      rtDest = @rawSP.destinations[1]
    result += ', ' + moment(dest.date).format('D.M.YYYY')
    if @rt()
      result += ' - ' + moment(rtDest.date).format('D.M.YYYY')
    result += ', ' + moment(dest.date).diff(moment(), 'days')
    if @rt()
      result += ' - ' + moment(rtDest.date).diff(moment(dest.date), 'days')
    return result

  findAndSelect: (result)=>
    if @noresults
      return
    result = @results().findAndSelect(result)
    if !result
      return false
    @_selectResult(result)
    return result

  select: (res)=>
    if !res?
      return
    # FIXME looks retardely stupid
    if res.ribbon
      #it is actually recommnd ticket
      res = res.data
    @_selectResult(res)

  _selectResult: (res)=>
    @results().selected_key res.key
    res.parent.filtersConfig = res.parent.filters.getConfig()
    @results().selected_best res.best | false
    @overviewTemplate = 'tours-overview-avia-ticket'
    @selection(res)

    
  toBuyRequest: =>
    sp = @observableSP()
    result = {}
    result.type = 'avia'
    result.searchId = @selection().cacheId
    # FIXME FIXME FXIME
    result.searchKey = @selection().flightKey()
    result.adults = sp.adt
    result.children = sp.chd
    result.infants = sp.inf
    return result

  doNewSearch: =>
    window.VisualLoaderInstance.start(@api.loaderDescription)
    @api.search @panel.sp.url(), (data)=>
      @newResults data.flights.flightVoyages, data.searchParams
      ko.processAllDeferredBindingUpdates()
      window.VisualLoaderInstance.hide()
  # Overview VM
  overviewText: =>
    "Перелет " + @results().departureCity + ' &rarr; ' + @results().arrivalCity

  overviewPeople: =>
    sum = @panel.sp.adults()+@panel.sp.children()+@panel.sp.infants()
    Utils.wordAfterNum(sum,'человек','человека','человек')


  numAirlines: =>
    # FIXME FIXME FIXME
    @results().filters.airline.options().length

  minPrice: =>
    if @noresults
      return 0
    cheapest = _.reduce @results().data,
      (el1, el2)->
        if el1.price < el2.price then el1 else el2
      ,@results().data[0]
    cheapest.price

  maxPrice: =>
    if @noresults
      return 0
    mostExpensive = _.reduce @results().data,
      (el1, el2)->
        if el1.price > el2.price then el1 else el2
      ,@results().data[0]
    mostExpensive.price
    

  # End overview VM

  destinationText: =>
    "<span class='left-avia-city'>" + @results().departureCity + "&rarr;</span> " + "<span class='left-avia-city'>" + @results().arrivalCity + "</span>"

  additionalText: =>
    if @selection() == null
      return ""
    if @rt()
      ""
    else
      ", " + @selection().departureTime() + ' - ' + @selection().arrivalTime()

  dateClass: =>
    if @rt() then 'blue-two' else 'blue-one'

  dateHtml: (startonly=false)=>
    sp = @observableSP()
    # FIXME SEARCH PARAMS
    result = '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth moment(sp.destinations[0].date)
    result+='</div>'
    if startonly
      return result
    if @rt()
      result+= '<div class="day">'
      result+= dateUtils.formatHtmlDayShortMonth moment(sp.destinations[1].date)
      result+= '</div>'
    return result

  timelineStart: =>
    sp = @observableSP()
    source = @selection()
    if source == null
      return sp.destinations[0].date
      source = @results().data[0]
    source.departureDate()

  timelineEnd: =>
    sp = @observableSP()
    source = @selection()
    if source == null
      return sp.destinations[0].date
    source.arrivalDate()

  rtTimelineStart: =>
    sp = @observableSP()
    source = @selection()
    if source == null
      return sp.destinations[1].date
    source.rtDepartureDate()

  rtTimelineEnd: =>
    sp = @observableSP()
    source = @selection()
    if source == null
      return sp.destinations[1].date
    source.rtArrivalDate()

  # Надо переименовать или зареюзать то что уже есть
  timelineEndDate: =>
    sp = @observableSP()
    source = @selection()
    if source == null
      return moment(sp.destinations[0].date).toDate()
    source.arrivalDate()

  timelineStartDate: =>
    sp = @observableSP()
    source = @selection()
    if source == null
      return moment(sp.destinations[0].date).toDate()
    source.departureDate()
        
  rt: =>
    sp = @observableSP()
    sp.destinations.length == 2

  beforeRender: =>
    if @results().selectedKey
      @results().filters.getConfig(@results().filtersConfig)

  afterRender: =>
    if @results()
      if @results().selected_key
        window.setTimeout(
          =>
            if $('.ticket-content .pressButton.selected').parent().parent().parent().parent().length
              Utils.scrollTo($('.ticket-content .pressButton.selected').parent().parent().parent().parent())

          , 50
        )

       
class ToursHotelsResultSet extends TourEntry
  constructor: (raw, sp)->
    super
    @api = new HotelsAPI
    @panel = new HotelsPanel()
    @panel.handlePanelSubmit = @doNewSearch
    @panel.sp.fromObject sp
    @panel.original_template = @panel.template
    @overviewTemplate = 'tours-overview-hotels-no-selection'
    @template = 'hotels-results'

    @activeHotel = ko.observable 0
    @selection = ko.observable null
    @results = ko.observable()
    @data = {results: @results}
    @observableSP = ko.observable()
    
    @savingsWithAviaOnly = true

    @newResults raw, sp

  newResults: (data, sp)=>
    @rawSP = sp
    @observableSP sp
    
    result = new HotelsResultSet data, sp, @activeHotel
    result.tours true
    result.postInit()
    result.select = (hotel) =>
      hotel.parent = result
      hotel.oldPageTop = $("html").scrollTop() | $("body").scrollTop()
      hotel.off 'back'
      hotel.on 'back', =>
        @trigger 'setActive', @, false, false,hotel.oldPageTop, =>
          if !hotel.parent.showFullMap()
            Utils.scrollTo('#hotelResult'+hotel.hotelId)
          else
            hotel.parent.showFullMapFunc(null,null,true)
      hotel.getFullInfo()
      hotel.off 'select'
      hotel.on 'select', (roomData) =>
        @select roomData
        @trigger 'next'
      @trigger 'setActive', {'data':hotel, template: 'hotels-info-template', 'parent':@}
    result.selectFromPopup = (hotel) =>
      hotel.parent = result
      hotel.activePopup.close()
      hotel.oldPageTop = $("html").scrollTop() | $("body").scrollTop()
      hotel.off 'back'
      hotel.on 'back', =>

        @trigger 'setActive', @, false, false, hotel.oldPageTop,=>
          if !hotel.parent.showFullMap()
            Utils.scrollTo('#hotelResult'+hotel.hotelId)
      hotel.off 'select'
      hotel.on 'select', (roomData) =>
        @select roomData
        @trigger 'next'
      @trigger 'setActive', {'data':hotel, template: 'hotels-info-template', 'parent':@}
    # FIXME WTF
    @hotels = true
    @selection null
    @noresults = result.noresults
    @results result

  GAKey: =>
    @rawSP.city

  GAData: =>
    result = "1"
    passangers = [0, 0, 0]
    for room in @rawSP.rooms
      passangers[0] += room.adt*1
      passangers[1] += room.chd*1
      passangers[2] += room.cots*1
    result += ", " + passangers.join(" - ")
    result += ", " + moment(@rawSP.checkIn).format('D.M.YYYY') + ' - ' + moment(@rawSP.checkIn).add(@rawSP.duration, 'days').format('D.M.YYYY')
    result += ", " + moment(@rawSP.checkIn).diff(moment(), 'days') + " - " + @rawSP.duration
    return result


  findAndSelect: (result) =>
    if result
      if result.roomSet
        result = @results().findAndSelect(ko.utils.unwrapObservable(result.roomSet))
      else
        result = @results().findAndSelect(ko.utils.unwrapObservable(result.roomSets)[0])
    if !result
      return false
    @_selectRoomSet result

  findAndSelectSame: (result) =>
    if @results() && @results().data() && @results().data().length
      if result.roomSet
        ret = @results().findAndSelectSame(ko.utils.unwrapObservable(result.roomSet))
      else
        ret = @results().findAndSelectSame(ko.utils.unwrapObservable(result.roomSets)[0])
      if !ret
        ret = @results().findAndSelectSameParams(result.categoryId,result.getLatLng())
      @_selectRoomSet ret
      
  select: (roomData)=>
    if roomData?
      @_selectRoomSet roomData.roomSet

  _selectRoomSet: (roomSet)=>
    hotel = roomSet.parent
    hotel.parent = @results()
    @activeHotel  hotel.hotelId
    @overviewTemplate = 'tours-overview-hotels-ticket'
    @selection {roomSet: roomSet, hotel: hotel}
    hotel.parent.filtersConfig = hotel.parent.filters.getConfig()
    #Код для того чтобы выбранный результат попал в отображение -->
    limit = 0
    sortKey = hotel.parent.sortBy()
    ordKey = hotel.parent.ordBy()


    hotel.parent.data.sort (left, right)=>
      if left[sortKey] < right[sortKey]
        return -1 * ordKey
      if left[sortKey] > right[sortKey]
        return  1 * ordKey
      return 0

    for result in hotel.parent.data()
      if result.visible()
        limit++
        if result.hotelId == hotel.hotelId
          break

    pageFound = Math.ceil(limit/hotel.parent.showLimit)
    # <--
    if pageFound > hotel.parent.showParts()
      hotel.parent.pagesLoad = pageFound
    else
      hotel.parent.pagesLoad = hotel.parent.showParts()


  toBuyRequest: =>
    result = {}
    result.type = 'hotel'
    result.searchId = @selection().hotel.cacheId
    # FIXME FIXME FXIME
    result.searchKey = @selection().roomSet.resultId
    result.adults = 0
    result.age = false
    result.cots = 0
    sp = @observableSP()
    for room in sp.rooms
      result.adults += room.adultCount*1
      # FIXME looks like this could be array
      if room.childAge
        result.age = room.childAgeage
      
      result.cots += room.cots*1
    return result


  doNewSearch: =>
    window.VisualLoaderInstance.start(@api.loaderDescription)
    @api.search @panel.sp.url(), (data)=>
      data.searchParams.cacheId = data.cacheId
      @newResults data, data.searchParams
      ko.processAllDeferredBindingUpdates()
      window.VisualLoaderInstance.hide()


  # Overview VM
  overviewText: =>
    @destinationText()

  overviewPeople: =>
    sum = @panel.sp.overall()
    Utils.wordAfterNum(sum,'человек','человека','человек') + ', ' + @results().wordDays

  numHotels: =>
    @results().data().length

  minPrice: =>
    @results().minPrice

  maxPrice: =>
    @results().maxPrice

  # end Overview VM

  # tours overview
  destinationText: =>
    # trigger dependencies
    sp = @observableSP()
    if @noresults
      sp.cityFull.caseNom
    else
      "<span class='hotel-left-long'>Отель в " + sp.cityFull.casePre + "</span><span class='hotel-left-short'>" + sp.cityFull.caseNom + "</span>"

  price: =>
    if @selection() == null
      return 0

    @selection().roomSet.price

#  overviewPriceHtml: =>
#    result =', ' + @selection().hotel.wordDays
#    result += @price() + '<span class="rur">o</span>'
#    return result
    
  additionalText: =>
    if @selection() == null
      return ""
    ", " + @selection().hotel.hotelName

  dateClass: =>
    'orange-two'

  dateHtml: (startOnly=false)=>
    result = '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth @results().checkIn
    result+='</div>'
    if startOnly
      return result
    result+= '<div class="day">'
    result+= dateUtils.formatHtmlDayShortMonth @results().checkOut
    result+= '</div>'

  timelineStart: =>
    @results().checkIn

  timelineEnd: =>
    @results().checkOut

  timelineStartDate: =>
    @results().checkIn._d

  timelineEndDate: =>
    @results().checkOut._d

  beforeRender: =>
    if @results()
      @results().toursOpened = true
      if @activeHotel()
        @results().filters.setConfig(@results().filtersConfig)
        @results().showParts(@results().pagesLoad)
      else
        @results().postFilters()

  afterRender: =>
    if @results()
      if @activeHotel()
        window.setTimeout(
          =>
            if $('.hotels-tickets .pressButton.selected').parent().parent().parent().parent().length
              Utils.scrollTo($('.hotels-tickets .pressButton.selected').parent().parent().parent().parent())

          , 50
        )

  savings: =>
    if(@selection()==null)
      return 0
    @selection().roomSet.price - @selection().roomSet.discountPrice
    

class ToursResultSet
  constructor: (raw, @searchParams)->
    # Mix in events
    _.extend @, Backbone.Events
    @creationMoment = moment()

    @data = ko.observableArray()
    for variant in raw.allVariants
      if !variant
        continue
      if variant.flights
        result =  new ToursAviaResultSet variant.flights.flightVoyages, variant.searchParams
      else
        result = new ToursHotelsResultSet variant, variant.searchParams
      @data.push result
      result.on 'setActive', (entry, beforeRender = true, afterRender = true, scrollTo = 0,callback = null)=>
        @setActive entry, beforeRender, afterRender, scrollTo, callback
      result.on 'next', (entry)=>
        @nextEntry()

    @timeline = new Timeline(@data)
    @selection = ko.observable @data()[0]
    
    @panel = ko.computed 
      read: =>
        if @selection().panel
          @panelContainer = @selection().panel
        # FIXME this should never happen but it does
        if ! @panelContainer.minimizedCalendar?
          @panelContainer.minimizedCalendar = -> true
        @panelContainer.timeline = @timeline
        @panelContainer.setActiveTimelineAvia = @setActiveTimelineAvia
        @panelContainer.setActiveTimelineHotels = @setActiveTimelineHotels
        
        if !@panelContainer.onlyTimeline
          @panelContainer.onlyTimeline = false
          @panelContainer.timeline.termsActive = true
        else
          @panelContainer.timeline.termsActive = false
        @panelContainer.selection = @selection
        @panelContainer.template = 'tours-panel-template'

        return @panelContainer

    @price = ko.computed =>
      sum = 0
      for item in @data()
        sum += item.price()
      return sum

    @savings = ko.computed =>
      has_avia = false
      for item in @data()
        if item.selection() && item.isAvia()
          has_avia = true
        

      sum = 0
      for item in @data()
        if item.savingsWithAviaOnly 
          if has_avia
            sum+=item.savings()
        else
          sum += item.savings()
      return sum

    @someSegmentsSelected= ko.computed =>
      for x in @data()
        if x.selection()
          return true
      return false

    @someSegmentsSelected.subscribe (newValue)=>
      if newValue
        $('#tour-buy-btn').show 'fast'
      else
        $('#tour-buy-btn').hide 'fast'
      
    @vm = new ToursOverviewVM @

    # FIXME have to resorn after their run
    # set of predefined algoriths for tour selection
    @voyashki = []
    @voyashki.push new VoyashaCheapest @
    @voyashki.push new VoyashaOptima @
    @voyashki.push new VoyashaRich @
    
  setActive: (entry,beforeRender = true, afterRender = true,scrollTo = 0,callback = null)=>
    $('#loadWrapBgMin').show()
    if entry.overview
      $('.btn-timeline-and-condition').hide()
      window.toursOverviewActive = true
    else
      window.toursOverviewActive = false
    if entry.beforeRender && beforeRender
      entry.beforeRender()
    @trigger 'inner-template', entry.template
    # FIXME 
    window.setTimeout =>
      if entry.afterRender && afterRender
        entry.afterRender()
      @selection entry
      ko.processAllDeferredBindingUpdates()
      ResizeAvia()
      $('#loadWrapBgMin').hide()
      if !(scrollTo == false)
        Utils.scrollTo(scrollTo,false)
      if callback
        callback()
    , 100


  setActiveTimelineAvia: (entry)=>
    @setActive entry.avia.item

  setActiveTimelineHotels: (entry)=>
    @setActive entry.hotel.item

  nextEntry: =>
    for x in @data()
      if !x.selection()
        @setActive x
        return
    return @showOverview()

  removeItem: (item, event)=>
    event.stopPropagation()
    if @data().length <2
      return
    idx = @data.indexOf(item)

    if idx ==-1
      return
    @data.splice(idx, 1)
    if item == @selection()
      @setActive @data()[0]

  showOverview: =>
    dummyPanel =
      onlyTimeline: true
      calendarHidden: -> true
      calendarValue: ko.observable {values: []}
    @setActive {template: 'tours-overview', data: @, overview: true, panel: dummyPanel}
    do ResizeAvia
    window.setTimeout(
      ()=>
        people = 0
        calendarEvents = []
        for resSet in @data()
          sp = resSet.observableSP()
          if resSet.isAvia()
            flights = []
            if people==0
              people = sp.adt + sp.chd + sp.inf

            for dest in sp.destinations
              flight = {type: 'flight',description:  dest.departure+' || ' + dest.arrival, cityFrom: dest.departure_iata, cityTo: dest.arrival_iata}
              flight.dayStart = moment(dest.date)._d
              flight.dayEnd = moment(dest.date)._d
              flights.push flight

            if resSet.selection()
              aviaRes = resSet.selection()
              flights[0].dayEnd = aviaRes.arrivalDate();
              if aviaRes.roundTrip
                flights[1].dayEnd = aviaRes.rtArrivalDate();
            for flight in flights
              calendarEvents.push flight
          if resSet.isHotel()

            if people==0
              people = sp.overall()

            checkIn = moment(sp.checkIn).add('h',8);
            checkOut = moment(sp.checkIn).add('d',sp.duration);

            hotelEvent = {dayStart: checkIn._d,dayEnd: checkOut._d,type: 'hotel',description:  '', city: sp.city}
            if resSet.selection()
              hotelEvent.description = resSet.selection().hotel.hotelName

            calendarEvents.push hotelEvent
        calendarEvents.sort(
          (left,right)->
            if left.dayStart > right.dayStart
              return 1
            if left.dayStart < right.dayStart
              return -1
            return 0
        )

        VoyangaCalendarTimeline.calendarEvents = calendarEvents;
        VoyangaCalendarTimeline.jObj = '#voyanga-calendar-timeline'
        VoyangaCalendarTimeline.init()

        #подготавливаем текст шаринга
        tmp = []
        _.each calendarEvents, (e) ->
          if e.type == 'flight'
            tmp.push e.description.replace('||', '→').replace(/^\s+|\s+$/g, '')
          else if e.type == 'hotel'
            duration = Math.ceil((e.dayEnd - e.dayStart) / (1000 * 60 * 60 * 24)) #ночей в отеле
            tmp.push 'отель ' + e.description.replace(/^\s+|\s+$/g, '') + '(' + Utils.wordAfterNum(duration, 'ночь', 'ночи', 'ночей') + ')'
        interval = dateUtils.formatDayMonthInterval calendarEvents[0].dayStart, _.last(calendarEvents).dayEnd
        tmp.push interval
        tmp.push (@price() - @savings()) + ' руб. ' + Utils.peopleReadable(people)

        title = "Я составил путешествие на Voyanga"
        description = tmp.join(', ')

        #готовим почву для генерации ссылки
        hash = dateUtils.formatDayMonthInterval calendarEvents[0].dayStart, _.last(calendarEvents).dayEnd
        hash += (@price() - @savings()) + people
        for el in @data()
          cur = el.selection()
          if cur && el.isAvia()
            hash += cur.similarityHash()
          else if cur && el.isHotel()
            hash += cur.hotel.hotelId + cur.roomSet.similarityHash()

        data = $.extend {}, {hash: hash, name: description}, @createTourData()

        $.post '/ajax/getSharingUrl', data, (response) ->
          #показываем кнопки шаринга
          url = response['short']
          $('.shareSocial').html('')
          $('.socialSharePlaceholder').clone(true).show().appendTo('.shareSocial')
          $('.shareSocial').find('input[name=textTextText]').val(url)
          $('.shareSocial').show().find('a').each ()->
            $(this).attr('addthis:title', title)
            $(this).attr('addthis:description', description)
            $(this).attr('addthis:url', url)
            addthis.toolbox('.socialSharePlaceholder')
      ,1000
    )

  createTourData: =>
    toBuy = []
    data = {}
    for x in @data()
      if x.selection()
        toBuy.push {module: 'Tours'}
        toBuy.push x.toBuyRequest()
    for params, index in toBuy
      for key,value of params
        key = "item[#{index}][#{key}]"
        data[key] = value
    data

  buy: =>
    ticketValidCheck = $.Deferred()
    GAAviaKeys = []
    GAHotelKeys = []
    GAAviaData = []
    GAHotelData = []
    hasAvia = false
    hasHotel = false
    ticketValidCheck.done (resultSet)=>
      toBuy = []
      for x in resultSet.data()
        if x.selection()
          if x.isAvia()
            GAAviaKeys.push x.GAKey()
            GAAviaData.push x.GAData()
            hasAvia = true
          if x.isHotel()
            GAHotelKeys.push x.GAKey()
            GAHotelData.push x.GAData()
            hasHotel = true
          toBuy.push {module: 'Tours'}
          toBuy.push x.toBuyRequest()
      if hasHotel
        GAPush ['_trackEvent', 'Trip_press_button_buy', GAHotelKeys.join('//'),  GAHotelData.join('//'), "", true]
      else if hasAvia
        GAPush ['_trackEvent', 'Avia_press_button_buy', GAAviaKeys.join('//'),  GAAviaData.join('//'), "", true]
      Utils.toBuySubmit toBuy
      
    @checkTicket @data(), ticketValidCheck

  findAndSelect: (data) =>
    success = true
    for entry, index in data
      if !@data()[index].findAndSelect(entry.selection())
        success = false
    return success

  # FIXME ищет в пустых результатах, пока костыльнул.
  findAndSelectItems: (items) =>
    # В теории не должно никогда случаться, на практике видимо случается:
    # Количество сегментов в результате от апи не равно количеству элементов
    # в туре. Оттуда @data()[index] далее по тексту undefined.
    if items.length != @data().length
      return false
      
    success = true

    @data.sort(
      (left, right)->
        leftDate = dateUtils.formatDayMonthYear(left.timelineStartDate())
        rightDate = dateUtils.formatDayMonthYear(right.timelineStartDate())
        if leftDate == rightDate
          if left.isAvia() != right.isAvia()
            return left.timelineEndDate() > right.timelineEndDate() ? -1 : 1;
        return left.timelineStartDate() > right.timelineStartDate() ? -1 : 1;
    )
        #if
        #return left.lastName == right.lastName ? 0 : (left.lastName < right.lastName ? -1 : 1)
    for entry, index in items
      if(@data()[index].isAvia())
        result = @data()[index].findAndSelect(entry)
        if !result
          result = @data()[index].findAndSelect(@data()[index].results().cheapest())
        if !result
          success = false
      else
        result = @data()[index].findAndSelectSame(entry)
        if !result
          success = false

    return success
    
# decoupling some presentation logic from resultset
class ToursOverviewVM
  constructor: (@resultSet)->

  startCity: =>
    firstResult = @resultSet.data()[0]
    if firstResult.isAvia()
      firstResult.results().departureCity
    else
      firstResult.results().city.caseNom


  dateClass: =>
    'blue-one'
  
  
  dateHtml: =>
    firstResult = @resultSet.data()[0]
    return firstResult.dateHtml(true)

  afterRender: =>

class TourTripResultSet
  constructor: (@resultSet) ->
    @items = []
    @cities = []
    @hasFlight = false
    @hasHotel = false
    @flightCounter = ko.observable 0
    @hotelCounter = ko.observable 0
    @selected_key = ko.observable ''
    @selected_best = ko.observable ''
    @totalCost = 0
    @totalCostWithDiscount = 0
    @totalCostWithoutDiscount = 0
    @tour = false
    @additional = false
    @flightIds = ko.observableArray([])
    @flightIdsString = ko.computed =>
      resArr = @flightIds()
      return resArr.join(':')
    @showTariffRules = =>
      aviaApi = new AviaAPI();
      window.VisualLoaderInstance.start  'Загружаем правила применения тарифов'
      aviaApi.search 'flight/search/tariffRules?flightIds='+@flightIdsString(),
        (data)=>
          if(data)
            tariffs = []
            for item in @items
              if(item.isFlight && data[item._data.flightKey])
                tariff = {}
                tariff.route = "Перелет из "+item.departureCity()+" в " + item.arrivalCity()
                tariff.codes = []
                for key,code of data[item._data.flightKey]
                  tariff.codes.push code
                tariffs.push tariff
            if tariffs
              gp = new GenericPopup('#tariff-rules',{'tariffs': tariffs})
          window.VisualLoaderInstance.hide()

    @flightCounterWord = ko.computed =>
      if @flightCounter()==0
        return
      res = Utils.wordAfterNum  @flightCounter(), 'авиабилет', 'авиабилета', 'авиабилетов'
      if (@hotelCounter()>0)
        res = res + ', '
      return res

    @hotelCounterWord = ko.computed =>
      if @hotelCounter()==0
        return
      Utils.wordAfterNum  @hotelCounter(), 'гостиница', 'гостиницы', 'гостиниц'

    _.each @resultSet.items, (item) =>
      if (item.isFlight)
        @tour = true
        @hasFlight = true
        @flightCounter(@flightCounter()+1)
        @roundTrip = item.flights.length == 2
        aviaResult = new AviaResult(item, @)
        @flightIds.push aviaResult._data.flightKey
        aviaResult.sort()
        aviaResult.totalPeople = Utils.wordAfterNum item.searchParams.adt + item.searchParams.chd + item.searchParams.inf, 'человек', 'человека', 'человек'
        aviaResult.totalPeopleGen = Utils.wordAfterNum item.searchParams.adt + item.searchParams.chd + item.searchParams.inf, 'человека', 'человек', 'человек'
        aviaResult.rawSP = item.searchParams
        if (@roundTrip)
          @cities.push {isLast: false, cityName: item.flights[0].departureCity}
          @cities.push {isLast: false, cityName: item.flights[0].arrivalCity}
          @additional = {isLast: false, cityName: item.flights[0].departureCity}
        else
          @cities.push {isLast: false, cityName: item.flights[0].departureCity}
          @cities.push {isLast: false, cityName: item.flights[0].arrivalCity}
        @items.push aviaResult
        @totalCostWithDiscount += aviaResult.price
        @totalCostWithoutDiscount = @totalCostWithDiscount
      else if (item.isHotel)
        @hasHotel = true
        @hotelCounter(@hotelCounter()+1)
        @lastHotel = new HotelResult item, @, item.duration, item, item.hotelDetails
        @cities.push {cityName: @lastHotel.activeHotel.city}
        totalPeople = 0
        # no side effects here ?!
        _.each item.searchParams.rooms, (room) -> totalPeople += room.adultCount/1 + room.childCount/1 + room.cots/1
        @lastHotel.rawSP = item.searchParams
        @lastHotel.totalPeople = Utils.wordAfterNum totalPeople, 'человек', 'человека', 'человек'
        @lastHotel.totalPeopleGen = Utils.wordAfterNum totalPeople, 'человека', 'человек', 'человек'
        @items.push(@lastHotel)
        @totalCostWithDiscount += @lastHotel.roomSets()[0].discountPrice
        @totalCostWithoutDiscount += @lastHotel.roomSets()[0].price

    if (@additional)
      @cities.push @additional

    newCity = []
    _.each @cities, (city, i) =>
        a = _.last(newCity)
        if not _.isObject(a)
          newCity.push city
        if _.last(newCity).cityName != city.cityName
          newCity.push city
    @cities = newCity
    _.each @cities, (city, i) =>
      if (i == (@cities.length - 1))
        city.isLast = true
      else
        city.left = Math.round((100 / @cities.length) * (i + 1) - 8.4);
        if (city.left<0)
          city.left =  '0%'
        else
          city.left = city.left + '%'
    if @tour
        @totalCost = @totalCostWithDiscount
    else
        @totalCost = @totalCostWithoutDiscount

  trackBuyClick: =>
    # Отпавляем в гугол инфу о нажатии на кнопку перейти к оплате
    if @hasFlight && @items.length == 1
      # Один самолет -> пришли с авиа
      aviaResult = @items[0]
      GAPush ['_trackEvent', 'Avia_press_button_data', aviaResult.GAKey(), aviaResult.GAData(), aviaResult.airline, true]
      return
      
    if @hasHotel && @items.length == 1
      # Один отель -> пришли с отелей
      hotelResult = @items[0]
      GAPush ['_trackEvent', 'Hotel_press_button_data', hotelResult.GAKey(), hotelResult.GAData(), hotelResult.hotelName, true]
      return

    # almost copy of tobuy
    GAAviaKeys = []
    GAHotelKeys = []
    GAAviaData = []
    GAHotelData = []
    GAAviaExtra = []
    GAHotelExtra = []
    hasAvia = false
    hasHotel = false
    for x in @items
      if x.isFlight
        GAAviaKeys.push x.GAKey()
        GAAviaData.push x.GAData()
        GAAviaExtra.push x.airline
        hasAvia = true
      if x.isHotel
        GAHotelKeys.push x.GAKey()
        GAHotelData.push x.GAData()
        GAHotelExtra.push x.hotelName
        hasHotel = true

    if hasHotel
      GAPush ['_trackEvent', 'Trip_press_button_data', GAHotelKeys.join('//'),  GAHotelData.join('//'), GAHotelExtra.join('//'), true]
    else if hasAvia
      GAPush ['_trackEvent', 'Avia_press_button_data', GAAviaKeys.join('//'),  GAAviaData.join('//'), GAAviaExtra.join('//'), true]
    

class TourResultSet
  constructor: (resultSet, orderId) ->
    @items = ko.observableArray([])
    @fullPrice = ko.observable 0
    @activePanel = ko.observable(null)
    @overviewPeople = ko.observable 0
    @orderId = orderId
    @overviewPricePeople = ko.observable('')
    @visiblePanel = ko.observable(true)
    @startCity = ko.observable ''
    @visiblePanel.subscribe (newValue)=>
      if newValue
        @showPanel()
      else
        @hidePanel()
    @showPanelText = ko.computed =>
      if @visiblePanel()
        return "свернуть"
      else
        return "развернуть"

    @reinit(resultSet)

  reinit: (@resultSet)=>
    @hasFlight = false
    @hasHotel = false
    @items([])
    @flightCounter = ko.observable 0
    @hotelCounter = ko.observable 0
    @selected_key = ko.observable ''
    @selected_best = ko.observable ''
    @correctTour = ko.observable false
    @overviewPeople = ko.observable 0
    @totalCost = 0
    panelSet = new TourPanelSet()
    @activePanel(panelSet)
    if @resultSet.items[0].isFlight
      startCity = @resultSet.items[0].searchParams.destinations[0].departure_iata
      startCityReadable = @resultSet.items[0].searchParams.destinations[0].departure
    else
      startCity = window.currentCityCode
      startCityReadable = window.currentCityCodeReadable
    @activePanel().startCity(startCity)
    @activePanel().selectedParams = {ticketParams: [], orderId: @orderId}
    @activePanel().sp.calendarActivated(false)
    window.app.fakoPanel(panelSet)

    @startCity(startCityReadable)
    @flightCounterWord = ko.computed =>
      res = Utils.wordAfterNum @flightCounter(), 'авивабилет', 'авиабилета', 'авиабилетов'
      if (@hotelCounter() > 0)
        res = res + ', '
      return res
    @hotelCounterWord = ko.computed =>
      Utils.wordAfterNum @hotelCounter(), 'гостиница', 'гостиницы', 'гостиниц'

    try
      _.each @resultSet.items, (item) =>
        if (item.isFlight)
          @hasFlight = true
          @flightCounter(@flightCounter() + 1)
          @roundTrip = item.flights.length == 2
          aviaResult = new AviaResult(item, @)
          aviaResult.sort()
          aviaResult.priceHtml = ko.observable(Utils.formatPrice(aviaResult.price) + '<span class="rur">o</span>')
          aviaResult.overviewText = ko.observable("Перелет " + aviaResult.departureCity() + ' &rarr; ' + aviaResult.arrivalCity())
          aviaResult.overviewTemplate = 'tours-event-avia-ticket'
          aviaResult.dateClass = ko.observable(if @roundTrip then 'blue-two' else 'blue-one')
          aviaResult.isAvia = ko.observable(item.isFlight)
          aviaResult.isHotel = ko.observable(item.isHotel)
          aviaResult.startDate = aviaResult.departureDate()
          aviaResult.dateHtml = ko.observable('<div class="day">' + dateUtils.formatHtmlDayShortMonth(aviaResult.departureDate()) + '</div>' + (if @roundTrip then '<div class="day">' + dateUtils.formatHtmlDayShortMonth(aviaResult.rtDepartureDate()) + '</div>' else ''))
          @activePanel().selectedParams.ticketParams.push aviaResult.getParams()
          aviaResult.overviewPeople = ko.observable
          @items.push aviaResult
          @totalCost += aviaResult.price
        else if (item.isHotel)
          @hasHotel = true
          @hotelCounter(@hotelCounter() + 1)
          @lastHotel = new HotelResult item, @, item.duration, item, item.hotelDetails
          @lastHotel.priceHtml = ko.observable(Utils.formatPrice(@lastHotel.roomSets()[0].price) + '<span class="rur">o</span>')
          @lastHotel.dateClass = ko.observable('orange-two')
          @lastHotel.overviewTemplate = 'tours-event-hotels-ticket'
          @lastHotel.isAvia = ko.observable(item.isFlight)
          @lastHotel.isHotel = ko.observable(item.isHotel)
          @lastHotel.startDate = @lastHotel.checkIn
          @lastHotel.serachParams = item.searchParams
          @lastHotel.overviewText = ko.observable("<span class='hotel-left-long'>Отель в " + @lastHotel.serachParams.cityFull.casePre + "</span><span class='hotel-left-short'>" + @lastHotel.address + "</span>")
          @lastHotel.dateHtml = ko.observable('<div class="day">' + dateUtils.formatHtmlDayShortMonth(@lastHotel.checkIn) + '</div>' + '<div class="day">' + dateUtils.formatHtmlDayShortMonth(@lastHotel.checkOut) + '</div>')
          @activePanel().selectedParams.ticketParams.push @lastHotel.getParams()
          @items.push(@lastHotel)
          @totalCost += @lastHotel.roomSets()[0].discountPrice
      _.sortBy @items(), (item)->
        item.startDate

      @startDate = @items()[0].startDate
      @dateHtml = ko.observable('<div class="day">' + dateUtils.formatHtmlDayShortMonth(@startDate) + '</div>')
      firstHotel = true
      for item in @items()
        if item.isHotel()
          if !firstHotel
            @activePanel().addPanel(true)
          else
            i = 0
            for room in item.serachParams.rooms
              if !@activePanel().sp.rooms()[i]
                @activePanel().sp.addSpRoom()
              @activePanel().sp.rooms()[i].adults(room.adultCount)
              @activePanel().sp.rooms()[i].children(room.childCount)
              @activePanel().sp.rooms()[i].ages(room.childAge)
              i++
            firstHotel = false

          @activePanel().lastPanel.checkIn(moment(item.checkIn)._d)
          @activePanel().lastPanel.checkOut(moment(item.checkOut)._d)
          @activePanel().lastPanel.city(item.cityCode)

      @activePanel().saveStartParams()
      _.last(@activePanel().panels()).minimizedCalendar(true)

      @overviewPeople(Utils.wordAfterNum(@activePanel().sp.overall(), 'человек', 'человека', 'человек'))

      setTimeout ()=>
        @activePanel().sp.calendarActivated(true)
      , 1000

      window.setTimeout ()=>
        if @visiblePanel()
          $('.sub-head.event').css('margin-top', '0px')
        else
          $('.sub-head.event').stop(true).css('height', (@activePanel().heightPanelSet()) + 'px').css('margin-top', (-@activePanel().heightPanelSet() + 4) + 'px')
      , 200
      @correctTour(true)
    catch exept
      @correctTour(false)

    if @resultSet.price
      @totalCost = @resultSet.price
    @fullPrice(@totalCost)

  gotoAndShowPanel: =>
    Utils.scrollTo('.panel')
    @visiblePanel(true)
  togglePanel: =>
    @visiblePanel(!@visiblePanel())
  showPanel: =>
    $('.sub-head.event').animate(
      {'margin-top': '0px'},
      ->
        $('.tdCity .add-tour').show()
    )
  hidePanel: =>
    $('.tdCity .add-tour').hide()
    $('.sub-head.event').css('height', (@activePanel().heightPanelSet()) + 'px').animate({'margin-top': (-@activePanel().heightPanelSet() + 4) + 'px'})

