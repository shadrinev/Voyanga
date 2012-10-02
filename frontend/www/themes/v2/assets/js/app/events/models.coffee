ko.bindingHandlers.highlightChange =
  update: (element, valueAccessor, allBindingsAccessor) ->

    # First get the latest data that we're bound to
    value = valueAccessor()
    allBindings = allBindingsAccessor()

    # Next, whether or not the supplied model property is observable, get its current value
    valueUnwrapped = ko.utils.unwrapObservable(value)

    # Grab some more data from another binding property
    previousImage = allBindings.previousImage

    console.log(previousImage(), valueUnwrapped)

    newEl = $('<div class="IMGmain"><img src=""></div>')
    newEl.appendTo ".centerTours"

    $(".IMGmain").eq(0).find('img').attr "src", previousImage()

    varLeftPos = $(".IMGmain").eq(1).css("left")
    varTopPos = $(".IMGmain").eq(1).css("top")
    varLeftPos = parseInt(varLeftPos.slice(0, -2))
    varTopPos = parseInt(varTopPos.slice(0, -2))
    varLeftPosStart = varLeftPos
    varTopPosStart = varTopPos

    $(".IMGmain").eq(1).css("opacity", "0").css("left", varLeftPosStart + "px").css("top", varTopPosStart + "px").find("img").attr "src", valueUnwrapped
    previousImage(valueUnwrapped)

    $(".IMGmain").eq(1).find("img").load ->
      $(".IMGmain").eq(0).animate
        opacity: 0
      , speedAnimateChangePic, ->
        $(".IMGmain:not(:last-child)").eq(0).remove()

      $(".IMGmain").eq(1).animate
        opacity: 1
        left: varLeftPos + "px"
        top: varTopPos + "px"
      , speedAnimateChangePic, ->
        setTimeout () ->
          startCount = 0
        , 100

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
    @tour = ko.observable new EventTour(data.tour)
    @image = ko.observable data.image
    @thumb = ko.observable data.thumb
    @active = ko.observable data.active
    @minimalPrice = ko.computed =>
      @prices()[0].price

  duration: ->
    dateUtils.formatDuration @_duration

class EventSet
  constructor: (events) ->
    @events = ko.observableArray events
    @currentEvent = ko.computed =>
      activeEvents = _.filter @events(), (event) ->
        return event.active()
      return activeEvents[0]
    @previousImage = ko.observable ''

  setActive: (valueAccessor, event) =>
    $('.slideTours').find('.triangle').animate {'top' : '0px'}, 200
    @events(_.map @events(), (event) =>
      event.active(false)
    )
    valueAccessor.active(true)
    $(event.target).closest('.toursTicketsMain').find('.triangle').animate {'top' : '-17px'}, 200

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

window.eventsRaw =
  [
    {
    "active": true,
    "startDate": "Иван",
    "endDate": "Иванов",
    "address": "Московское ш., 101, кв.101, Ленинград, 101101"
    "contact": "812 123-1234",
    "thumb": "http://img1-fotki.yandex.net/get/6412/64844073.1d/0_STATIC87fc9_6cd8e943_L",
    "preview": "Превью будет здесь",
    "description": "Здесь будет описание",
    "image": "http://img-fotki.yandex.ru/get/6412/64844073.1d/0_87fc9_6cd8e943_XXL",
    "title": "Осень",
    "categories": [
      {
      "id": 1,
      "title": "Осень"
      }
    ],
    "links": [
      {
      "title": "Осень"
      "url": "http://ya.ru"
      }
    ],
    "tags": [
      {
      "name": "tag1"
      },
      {
      "name": "tag2"
      }
    ],
    "prices": [
      {
      "city":
        {
        "title": 'Москва'
        }
      "price": 12500
      }
    ],
    "tour": [
      "name": 'Отдых за 12500'
    ]
    },

    {
    "active": false,
    "startDate": "Иван",
    "endDate": "Иванов",
    "address": "Московское ш., 101, кв.101, Ленинград, 101101"
    "contact": "812 123-1234",
    "thumb": "http://img-fotki.yandex.ru/get/6402/64844073.1d/0_881e6_4aa84b25_M",
    "preview": "Превью будет здесь новое и шикарное",
    "description": "Здесь будет описание",
    "image": "http://img-fotki.yandex.ru/get/6402/64844073.1d/0_881e6_4aa84b25_XXL",
    "title": "Трам пам пам",
    "categories": [
      {
      "id": 1,
      "title": "Осень"
      }
    ],
    "links": [
      {
      "title": "Осень"
      "url": "http://ya.ru"
      }
    ],
    "tags": [
      {
      "name": "tag1"
      },
      {
      "name": "tag2"
      }
    ],
    "prices": [
      {
      "city":
        {
        "title": 'Москва'
        }
      "price": 13244
      }
    ],
    "tour": [
      "name": 'Отдых за 12500'
    ]
    },

    {
    "active": false,
    "startDate": "Иван",
    "endDate": "Иванов",
    "address": "Московское ш., 101, кв.101, Ленинград, 101101"
    "contact": "812 123-1234",
    "thumb": "http://img-fotki.yandex.ru/get/6101/64844073.15/0_7ae13_5f74c202_M",
    "preview": "Превью будет здесь",
    "description": "Здесь будет описание",
    "image": "http://img-fotki.yandex.ru/get/6101/64844073.15/0_7ae13_5f74c202_orig",
    "title": "Лето",
    "categories": [
      {
      "id": 1,
      "title": "Осень"
      }
    ],
    "links": [
      {
      "title": "Осень"
      "url": "http://ya.ru"
      }
    ],
    "tags": [
      {
      "name": "tag1"
      },
      {
      "name": "tag2"
      }
    ],
    "prices": [
      {
      "city":
        {
        "title": 'Москва'
        }
      "price": 3850
      }
    ],
    "tour": [
      "name": 'Отдых за 12500'
    ]
    }
  ]

