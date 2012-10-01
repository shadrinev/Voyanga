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

  duration: ->
    dateUtils.formatDuration @_duration

class EventCategory
  constructor: (data) ->
    @id = ko.observable data.id
    @title = ko.observable data.title

class EventCategorySet
  constructor: (data) ->
    set = []
    data.each (i, eventCategory) ->
      set.push new EventCategory(eventCategory)

class EventLink
  constructor: (data) ->
    @title = ko.observable data.title
    @url = ko.observable data.url

class EventLinkSet
  constructor: (data) ->
    set = []
    data.each (i, eventLink) ->
      set.push new EventLink(eventLink)    

class EventTag
  constructor: (data) ->
    @name = ko.observable data.name

class EventTagSet
  constructor: (data) ->
    set = []
    data.each (i, eventTag) ->
      set.push new EventTag(eventTag)

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
    data.each (i, eventPrice) ->
      set.push new EventPrice(eventPrice)

events =
  [{
    "startDate": "Иван",
    "endDate": "Иванов",
    "address": "Московское ш., 101, кв.101 ,Ленинград, 101101"
    "contact":  "812 123-1234",
    "preview":  "<img src='http://img1-fotki.yandex.net/get/6412/64844073.1d/0_STATIC87fc9_6cd8e943_M'>",
    "description":  "<img src='http://img1-fotki.yandex.net/get/6412/64844073.1d/0_STATIC87fc9_6cd8e943_L'>",
    "title":  "Осень",
    "categories":  [{
      "id": 1,
      "title": "Осень"
      }],
  }]