ko.bindingHandlers.highlightChange =
  update: (element, valueAccessor, allBindingsAccessor) ->
    # First get the latest data that we're bound to
    value = valueAccessor()
    allBindings = allBindingsAccessor()

    # Next, whether or not the supplied model property is observable, get its current value
    valueUnwrapped = ko.utils.unwrapObservable(value)

    # Grab some more data from another binding property
    previousImage = allBindings.previousImage

    newEl = $('<div class="IMGmain"><img src=""></div>')
    newEl.appendTo ".centerTours"

    $(".IMGmain").eq(0).find('img').attr "src", previousImage()
    indexIMGresizeCenter(0)

    varLeftPos = $(".IMGmain").eq(1).css("left")
    varTopPos = $(".IMGmain").eq(1).css("top")
    varLeftPos = parseInt(varLeftPos.slice(0, -2))
    varTopPos = parseInt(varTopPos.slice(0, -2))
    varLeftPosStart = varLeftPos
    varTopPosStart = varTopPos

    $(".IMGmain").eq(1).css("opacity", "0").css("left", varLeftPosStart + "px").css("top", varTopPosStart + "px").find("img").attr "src", valueUnwrapped
    previousImage(valueUnwrapped)

    slideToursSlide()
    ResizeAvia()

    $(".IMGmain").eq(1).find("img").load ->
      indexIMGresizeCenter(1)
      $(".IMGmain").eq(0).animate
        opacity: 0
      , speedAnimateChangePic, ->
          $(".IMGmain:not(:last-child)").eq(0).remove()

      $(".IMGmain").eq(1).animate
        opacity: 1
      , speedAnimateChangePic

class Event extends Backbone.Events
  constructor: (data) ->
    @startDate = ko.observable new Date(data.startDate)
    @endDate = ko.observable new Date(data.endDate)
    @address = ko.observable data.address
    @contact = ko.observable data.contact
    @eventId = data.id
    @eventPageUrl = '/eventInfo/info/eventId/'+ @eventId
    @preview = ko.observable data.preview
    @description = ko.observable data.description
    @title = ko.observable data.title
    @categories = ko.observableArray new EventCategorySet(data.categories)
    @links = ko.observableArray new EventLinkSet(data.links)
    @tags = ko.observableArray new EventTagSet(data.tags)
    @prices = ko.observableArray new EventPriceSet(data.prices)
    @tour = ko.observable new EventTourSet(data.tours)
    @image = ko.observable data.image
    @thumb = ko.observable data.thumb
    @active = ko.observable data.active
    @minimalPrice = ko.computed =>
      @prices()[0].price

  duration: ->
    dateUtils.formatDuration @_duration

class EventSet
  constructor: (events) ->
    console.trace()
    @events = ko.observableArray events
    @currentTitle = ko.observable 'HUY'
    @currentEvent = ko.computed =>
      activeEvents = _.filter @events(), (event) ->
        event.active()
      console.log "SETTING TAITL", activeEvents[0].title()
      @currentTitle activeEvents[0].title()
      return activeEvents[0]
    @previousImage = ko.observable ''
    @activeMaps = 0;

  setActive: (valueAccessor, event) =>
    if($(event.target).hasClass('lookEyes'))
      return true
    if(@activeMaps == 1)
      @closeEventsMaps()

    $('.slideTours').find('.triangle').animate {'top' : '0px'}, 200
    @events(_.map @events(), (event) =>
      event.active(false)
    )
    valueAccessor.active(true)
    $(event.target).closest('.toursTicketsMain').find('.triangle').animate {'top' : '-16px'}, 200

  closeEventsPhoto: =>
    $(".slideTours").find(".active").find(".triangle").animate
      top: "0px"
    , 200
    $(".toursTicketsMain").removeClass "active"
    $(".mapsBigAll").css "opacity", "0"
    $(".toursBigAll").animate
      opacity: 0
    , 700, ->
      $(this).css "display", "none"

    $(".mapsBigAll").show()
    $(".mapsBigAll").animate
      opacity: 1
    , 700
    @activeMaps = 1

  closeEventsMaps: =>
    $(".toursBigAll").css "opacity", "0"
    $(".mapsBigAll").animate
      opacity: 0
    , 700, ->
      $(this).css "display", "none"

    $(".toursBigAll").show()
    $(".toursBigAll").animate
      opacity: 1
    , 700
    @activeMaps = 0

class EventCategory
  constructor: (data) ->
    @id = ko.observable data.id
    @title = ko.observable data.title

class EventCategorySet
  constructor: (data) ->
    set = []
    $.each data, (i, eventCategory) ->
      set.push new EventCategory(eventCategory)
    return set

class EventLink
  constructor: (data) ->
    @title = ko.observable data.title
    @url = ko.observable data.url

class EventLinkSet
  constructor: (data) ->
    set = []
    $.each data, (i, eventLink) ->
      set.push new EventLink(eventLink)
    return set

class EventTag
  constructor: (data) ->
    @name = ko.observable data.name

class EventTagSet
  constructor: (data) ->
    set = []
    $.each data, (i, eventTag) ->
      set.push new EventTag(eventTag)
    return set

class City
  constructor: (data) ->
    @title = ko.observable data.title

class EventPrice
  constructor: (data) ->
    @city = ko.observable new City(data.city)
    @price = ko.observable data.price

class EventPriceSet
  constructor: (data) ->
    set = []
    $.each data, (i, eventPrice) ->
      set.push new EventPrice(eventPrice)
    return set

class EventTour
  constructor: (data) ->
    @name = data.name

class EventTourSet
  constructor: (data) ->
    set = []
    $.each data, (i, tour) ->
      set.push new EventTour(tour)
    return set


class EventTourResultSet
  constructor: (resultSet,@eventId) ->
    @items = ko.observableArray([])
    @selectedCity = ko.observable(resultSet.city.id)
    @fullPrice = ko.observable 0
    @selectedCity.subscribe (newCityId)=>
      @reinit(window.toursArr[newCityId])
    @startCity = ko.observable resultSet.city.localRu
    @activePanel = ko.observable(null)
    @overviewPeople = ko.observable 0
    @overviewPricePeople = ko.observable('')
    @photoBox = new EventPhotoBox(window.eventPhotos)
    @visiblePanel = ko.observable(false)
    @visiblePanel.subscribe((newValue)=>
      if newValue
        @showPanel()
      else
        @hidePanel()
    )
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
    @totalCost = 0
    panelSet = new TourPanelSet()
    @activePanel(panelSet)
    @activePanel().startCity(@resultSet.city.code)
    @activePanel().selectedParams = {ticketParams:[],eventId:@eventId}
    @activePanel().sp.calendarActivated(false)
    window.app.fakoPanel(panelSet)

    @startCity(@resultSet.city.localRu)
    console.log('reinitEventData',@)
    @flightCounterWord = ko.computed =>
      res = Utils.wordAfterNum  @flightCounter(), 'авивабилет', 'авиабилета', 'авиабилетов'
      if (@hotelCounter()>0)
        res = res + ', '
      return res
    @hotelCounterWord = ko.computed =>
      Utils.wordAfterNum  @hotelCounter(), 'гостиница', 'гостиницы', 'гостиниц'

    try


      _.each @resultSet.items, (item) =>
        if (item.isFlight)
          @hasFlight = true
          @flightCounter(@flightCounter()+1)
          @roundTrip = item.flights.length == 2
          aviaResult = new AviaResult(item, @)
          aviaResult.sort()
          aviaResult.priceHtml = ko.observable(aviaResult.price + '<span class="rur">o</span>')
          aviaResult.overviewText = ko.observable("Перелет " + aviaResult.departureCity() + ' &rarr; ' + aviaResult.arrivalCity())
          aviaResult.overviewTemplate = 'tours-event-avia-ticket'
          aviaResult.dateClass = ko.observable(if @roundTrip then 'blue-two' else 'blue-one')
          aviaResult.isAvia = ko.observable(item.isFlight)
          aviaResult.isHotel = ko.observable(item.isHotel)
          aviaResult.startDate = aviaResult.departureDate()
          aviaResult.dateHtml = ko.observable('<div class="day">'+dateUtils.formatHtmlDayShortMonth(aviaResult.departureDate())+'</div>' + (if @roundTrip then '<div class="day">' + dateUtils.formatHtmlDayShortMonth(aviaResult.rtDepartureDate()) + '</div>' else '') )
          @activePanel().selectedParams.ticketParams.push aviaResult.getParams()

          aviaResult.overviewPeople = ko.observable

          @items.push aviaResult
          @totalCost += aviaResult.price
        else if (item.isHotel)
          @hasHotel = true
          @hotelCounter(@hotelCounter()+1)
          console.log "Hotel: ", item
          @lastHotel = new HotelResult item, @, item.duration, item, item.hotelDetails
          @lastHotel.priceHtml = ko.observable(@lastHotel.roomSets()[0].price + '<span class="rur">o</span>')

          @lastHotel.dateClass = ko.observable('orange-two')
          @lastHotel.overviewTemplate = 'tours-event-hotels-ticket'
          @lastHotel.isAvia = ko.observable(item.isFlight)
          @lastHotel.isHotel = ko.observable(item.isHotel)
          @lastHotel.startDate = @lastHotel.checkIn
          @lastHotel.serachParams = item.searchParams
          @lastHotel.overviewText = ko.observable("<span class='hotel-left-long'>Отель в " + @lastHotel.serachParams.cityFull.casePre + "</span><span class='hotel-left-short'>" + @lastHotel.address + "</span>")
          @lastHotel.dateHtml = ko.observable('<div class="day">' + dateUtils.formatHtmlDayShortMonth(@lastHotel.checkIn)+'</div>'+'<div class="day">' + dateUtils.formatHtmlDayShortMonth(@lastHotel.checkOut)+'</div>')
          @activePanel().selectedParams.ticketParams.push @lastHotel.getParams()
          console.log "Add to items hotel ", @lastHotel
          @items.push(@lastHotel)
          @totalCost += @lastHotel.roomSets()[0].discountPrice
      _.sortBy(
        @items(),
        (item)->
          return item.startDate
      )

      @startDate = @items()[0].startDate
      @dateHtml = ko.observable('<div class="day">' + dateUtils.formatHtmlDayShortMonth(@startDate)+'</div>')
      firstHotel = true
      console.log('items',@items())
      for item in @items()
        if item.isHotel()
          if !firstHotel
            @activePanel().addPanel()
          else
            #@activePanel().sp.rooms = item.serachParams.rooms
            #@activePanel().sp.rooms([])
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
          console.log('try set destData',moment(item.checkIn)._d,moment(item.checkOut)._d,item.cityCode,'to',@activePanel().lastPanel,@activePanel().lastPanel.checkIn())
      @overviewPeople(Utils.wordAfterNum(@activePanel().sp.overall(),'человек','человека','человек'))
      @overviewPricePeople(
        'Цена за ' +  (if @activePanel().sp.adults() then Utils.wordAfterNum(@activePanel().sp.adults(),'взрослого','взрослых','взрослых')
        else '') + (if @activePanel().sp.children() then ' '+Utils.wordAfterNum(@activePanel().sp.children(),'ребенка','детей','детей')
        else '')
      )
      console.log('activePanel',@activePanel())
      @activePanel().saveStartParams()
      _.last(@activePanel().panels()).minimizedCalendar(true)
      window.setTimeout(
        =>
          console.log('calendar activated')
          @activePanel().sp.calendarActivated(true)

        , 1000
      )

      window.setTimeout(
        =>
          if @visiblePanel()
            console.log('need showPanel')
            $('.sub-head.event').css('margin-top','0px')
          else
            $('.sub-head.event').stop(true);
            $('.sub-head.event').css('margin-top', (-@activePanel().heightPanelSet() + 4)+'px')
            console.log('need hidePanel',$('.sub-head.event'),@activePanel().heightPanelSet(),$('.sub-head.event').css('margin-top'))

        , 200
      )
      @correctTour(true)
    catch exept
      console.log("Cannot process tour")
      @correctTour(false)


    @fullPrice(@totalCost)
  gotoAndShowPanel: =>
    Utils.scrollTo('.panel')
    @visiblePanel(true)
  togglePanel: =>
    @visiblePanel(!@visiblePanel())
  showPanel: =>
    console.log('showPanel')
    $('.sub-head.event').animate({'margin-top': '0px'})
  hidePanel: =>
    console.log('hidePanel',@activePanel().heightPanelSet())
    $('.sub-head.event').animate({'margin-top': (-@activePanel().heightPanelSet() + 4)+'px'})



class EventPhotoBox
  constructor: (picturesRaw)->
    @photos = ko.observableArray([])
    @imagesServer = ko.observable ''
    @totalCount = 0
    @unloadedCount = 0

    @activeIndex = ko.observable(0)
    @picturesPadding = ko.observable(5)
    @animation = false
    @boxHeight = ko.observable(0)
    @picturesLoaded = false
    @afterRendered = false
    @renderedPhotos = ko.computed =>
      result = []

    pictures = []
    for photoObj in picturesRaw
      picture = new Image()

      @unloadedCount++;
      $(picture).bind(
        'load error',
        (e)=>
          console.log('image is loaded',e,@);
          if e.type == 'load'
            @totalCount++;
            photo = {}
            photo.url = e.currentTarget.src;
            photo.height = e.currentTarget.height;
            photo.width = e.currentTarget.width;
            photo.width = Math.round(photo.width * (400 / photo.height))
            photo.height = 400
            #if @boxHeight() < photo.height
            #  @boxHeight(photo.height)
            @boxHeight(400)
            @photos.push photo

          @unloadedCount--;
          if(@unloadedCount <= 0 )
            @picturesLoaded = true
            @afterLoad()
      )
      picture.src = @imagesServer() + photoObj.url
  getIndex: (ind)=>
    result = ind % @totalCount
    if result < 0
      result = @totalCount + result
    return result
  afterRender: =>
    @afterRendered = true
    @afterLoad()

  afterLoad: =>
    if @afterRendered && @picturesLoaded
      @renderedDivs = []
      console.log('phts',@photos(),@boxHeight())
      for i in [-2..2]
        divInfo = {}
        console.log('cmpW',i,'out',@getIndex(i))
        divInfo.div = $('<div class="eventPhoto"><img src="'+@photos()[@getIndex(i)].url+'" height="400"/></div>')
        divInfo.prevInd = @getIndex(i-1)
        divInfo.nextInd = @getIndex(i+1)
        divInfo.thisInd = @getIndex(i)
        @renderedDivs.push divInfo
      dw = Math.round(@photos()[@renderedDivs[2].thisInd].width / 2)
      tmpdw = dw+@picturesPadding()
      @renderedDivs[2].left = -dw
      for i in [3..4]
        @renderedDivs[i].left = tmpdw
        tmpdw += @photos()[@renderedDivs[i].thisInd].width + @picturesPadding()
      tmpdw = -dw
      for i in [1..0]
        tmpdw -= @photos()[@renderedDivs[i].thisInd].width + @picturesPadding()
        @renderedDivs[i].left = tmpdw

      for elem in @renderedDivs
        elem.div.css('left',elem.left + 'px')
        $('#eventsContent .photoGallery .centerPosition').append(elem.div);
      console.log('all loaded',@renderedDivs)
      $('.events .center-block').css('position','static')

  onAnimate: (pos,info)=>
    deltaLeft = pos - info.start
    for elem in @renderedDivs
      elem.div.css('left',(elem.left+deltaLeft) + 'px')


  onComplete: ()=>
    console.log('animation complete')
    for elem in @renderedDivs
      elem.left = elem.left + @delta
    if @delta < 0
      console.log('next')
      @renderedDivs[0].div.remove()
      @renderedDivs.shift()
      i = @renderedDivs[3].nextInd
      left = @renderedDivs[3].left + @photos()[@renderedDivs[3].thisInd].width + @picturesPadding()
    else
      console.log('prev')
      @renderedDivs[4].div.remove()
      @renderedDivs.pop()
      i = @renderedDivs[0].prevInd
      left = @renderedDivs[0].left - @photos()[i].width - @picturesPadding()

    divInfo = {}
    divInfo.div = $('<div class="eventPhoto"><img src="'+@photos()[@getIndex(i)].url+'" height="400"/></div>')
    divInfo.prevInd = @getIndex(i-1)
    divInfo.nextInd = @getIndex(i+1)
    divInfo.thisInd = @getIndex(i)
    divInfo.left = left;
    divInfo.div.css('left',divInfo.left+'px');

    if @delta < 0
      @renderedDivs.push(divInfo)
      $('#eventsContent .photoGallery .centerPosition').append(divInfo.div)
    else
      @renderedDivs.unshift(divInfo)
      $('#eventsContent .photoGallery .centerPosition').prepend(divInfo.div)
    console.log('divs',@renderedDivs)
    @animation = false


  onResize: =>
    console.log('resize')

  prev: =>
    if !@animation
      @animation = true
      dw = -Math.round(@photos()[@renderedDivs[1].thisInd].width / 2)
      @delta = dw - @renderedDivs[1].left
      console.log('delta',@delta)
      @renderedDivs[1].div.animate(
        {left: (dw) + 'px'},
        {
        step: (pos,info)=>
          @onAnimate(pos,info)
        ,
        complete: =>
          @onComplete()
        }
      )
  next: =>
    if !@animation
      @animation = true
      dw = -Math.round(@photos()[@renderedDivs[3].thisInd].width / 2)
      @delta = dw - @renderedDivs[3].left
      console.log('delta',@delta)
      @renderedDivs[3].div.animate(
        {left: (dw) + 'px'},
        {
        step: (pos,info)=>
          @onAnimate(pos,info)
        ,
        complete: =>
          @onComplete()

        }
      )

