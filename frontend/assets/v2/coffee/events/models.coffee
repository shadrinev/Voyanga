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
  constructor: (resultSet) ->
    @items = ko.observableArray([])
    @selectedCity = ko.observable(resultSet.city.id)
    @fullPrice = ko.observable 0
    @selectedCity.subscribe (newCityId)=>
      @reinit(window.toursArr[newCityId])
    @startCity = ko.observable resultSet.city.localRu

    @reinit(resultSet)

  reinit: (@resultSet)=>
    @hasFlight = false
    @hasHotel = false
    @items([])
    @flightCounter = ko.observable 0
    @hotelCounter = ko.observable 0
    @selected_key = ko.observable ''
    @selected_best = ko.observable ''
    @totalCost = 0
    @startCity(@resultSet.city.localRu)
    console.log('reinitEventData',@)
    @flightCounterWord = ko.computed =>
      res = Utils.wordAfterNum  @flightCounter(), 'авивабилет', 'авиабилета', 'авиабилетов'
      if (@hotelCounter()>0)
        res = res + ', '
      return res
    @hotelCounterWord = ko.computed =>
      Utils.wordAfterNum  @hotelCounter(), 'гостиница', 'гостиницы', 'гостиниц'

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
        aviaResult.isHotel = ko.observable(item.isisHotel)
        aviaResult.startDate = aviaResult.departureDate()
        aviaResult.dateHtml = ko.observable('<div class="day">'+dateUtils.formatHtmlDayShortMonth(aviaResult.departureDate())+'</div>' + (if @roundTrip then '<div class="day">' + dateUtils.formatHtmlDayShortMonth(aviaResult.rtDepartureDate()) + '</div>' else '') )

        @items.push aviaResult
        @totalCost += aviaResult.price
      else if (item.isHotel)
        @hasHotel = true
        @hotelCounter(@hotelCounter()+1)
        console.log "Hotel: ", item
        @lastHotel = new HotelResult item, @, item.duration, item, item.hotelDetails
        @lastHotel.priceHtml = ko.observable(@lastHotel.price + '<span class="rur">o</span>')
        @lastHotel.overviewText = ko.observable("<span class='hotel-left-long'>Отель в " + @lastHotel.address + "</span><span class='hotel-left-short'>" + @lastHotel.address + "</span>")
        @lastHotel.dateClass = ko.observable('orange-two')
        @lastHotel.overviewTemplate = 'tours-event-hotels-ticket'
        @lastHotel.isAvia = ko.observable(item.isFlight)
        @lastHotel.isHotel = ko.observable(item.isisHotel)
        @lastHotel.startDate = @lastHotel.checkIn
        @lastHotel.dateHtml = ko.observable('<div class="day">' + dateUtils.formatHtmlDayShortMonth(@lastHotel.checkIn)+'</div>'+'<div class="day">' + dateUtils.formatHtmlDayShortMonth(@lastHotel.checkOut)+'</div>')
        @items.push(@lastHotel)
        @totalCost += @lastHotel.roomSets()[0].discountPrice
    _.sortBy(
      @items(),
      (item)->
        return item.startDate
    )
    @startDate = @items()[0].startDate
    @dateHtml = ko.observable('<div class="day">' + dateUtils.formatHtmlDayShortMonth(@startDate)+'</div>')
    @fullPrice(@totalCost)
