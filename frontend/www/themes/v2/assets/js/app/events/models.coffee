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

  duration: ->
    dateUtils.formatDuration @_duration

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

eventsRaw =
  [
    {
    "startDate": "Иван",
    "endDate": "Иванов",
    "address": "Московское ш., 101, кв.101, Ленинград, 101101"
    "contact": "812 123-1234",
    "thumb": "http://img1-fotki.yandex.net/get/6412/64844073.1d/0_STATIC87fc9_6cd8e943_M",
    "preview": "Превью будет здесь",
    "description": "Здесь будет описание",
    "image": "http://img1-fotki.yandex.net/get/6412/64844073.1d/0_STATIC87fc9_6cd8e943_L",
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
    }
  ]

event = new Event(eventsRaw[0])
console.log "EVENT: ",event