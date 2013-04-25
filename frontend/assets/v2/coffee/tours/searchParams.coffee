
# Куда летим
class DestinationSearchParams
  constructor: ->
    @city = ko.observable ''
    @dateFrom = ko.observable ''
    @dateTo = ko.observable ''

# Куда летим
class ComplexSearchParams
  constructor: ->
    @segments = []
    @hotelId = ko.observable false
    @urlChanged = ko.observable(false)
    @hotelChanged = ko.observable(false)

  fromString: (data)->
    beforeUrl = @url()
    hotelIdBefore = @hotelId()
    data = PEGHashParser.parse(data,'tour')
    # FIXME if ! data.complex throw
    @segments = []
    for segment in data.segments
      if segment.avia
        sp = new AviaSearchParams
        sp.fromPEGObject segment
        @segments.push sp
      if segment.hotels
        sp = new HotelsSearchParams
        sp.fromPEGObject segment
        @segments.push sp

    wantedKeys = {eventId:1, orderId:1, flightHash:1}
    @hotelId(false)
    for pair in data.extra
      if wantedKeys[pair.key]
        @[pair.key] =  pair.value
      if pair.key == 'hotelId'
        @hotelId pair.value

    if beforeUrl == @url()
      @urlChanged(false)
      if hotelIdBefore == @hotelId()
        @hotelChanged(false)
      else
        @hotelChanged(true)
    else
      @urlChanged(true)
      @hotelChanged(false)


  fromTourData: (data) ->
    @segments = []
    for segment in data
      @segments.push segment.panel.sp

  url: ->
    result = "tour/search/complex?"
    params = []
    for segment,i in @segments
      for param in segment.getParams(true)
        params.push ("items[#{i}][" + param.replace("[", "][").replace("=","]=")).replace("]]","]").replace("[]","")
    result += params.join "&"
    return result

# Кто летит
class RoomsSearchParams
  constructor: ->
    @adt = ko.observable 2
    @chd = ko.observable 0
    @chdAge = ko.observable false
    @cots = ko.observable false

class SimpleSearchParams extends RoomsContainerMixin
  constructor: ->
    if(window.currentCityCode)
      @startCity = ko.observable window.currentCityCode
    else
      @startCity = ko.observable 'LED'
    @returnBack = ko.observable 1
    @destinations = ko.observableArray []
    # FIXME copy paste from hotel search params
    @rooms = ko.observableArray [new SpRoom(@)]
    @rooms()[0].adults(1)
    @hotelId = ko.observable(false)
    # FIXME не нужно быть обсерваблом
    @urlChanged = ko.observable(false)
    @hotelChanged = ko.observable(false)
    @overall = ko.computed =>
      result = 0
      for room in @rooms()
        result += room.adults()
        result += room.children()
      return result
    @adults = ko.computed =>
      result = 0
      for room in @rooms()
        result += room.adults()
      return result
    @children = ko.computed =>
      result = 0
      for room in @rooms()
        result += room.children()
      return result


  fromString: (data) =>
    data = PEGHashParser.parse(data,'tour')
    beforeUrl = @url()
    hotelIdBefore = @hotelId()
    @startCity data.start.from
    @returnBack data.start.rt
    @destinations []
    @rooms []
    for dest in data.destinations
      destination = new DestinationSearchParams()
      destination.city(dest.to)
      # Should we clone it
      destination.dateFrom(dest.dateFrom)
      destination.dateTo(dest.dateTo)
      @destinations.push destination

    for r in data.rooms
      room = new SpRoom(@)
      room.fromPEGObject(r)
      @rooms.push room

    wantedKeys = {eventId:1, orderId:1, flightHash:1}
    @hotelId(false)
    for pair in data.extra
      if wantedKeys[pair.key]
        @[pair.key] =  pair.value
      if pair.key == 'hotelId'
        @hotelId pair.value

    if beforeUrl == @url()
      @urlChanged(false)
      if hotelIdBefore == @hotelId()
        @hotelChanged(false)
      else
        @hotelChanged(true)
    else
      @urlChanged(true)
      @hotelChanged(false)

  fromObject: (data)->
    @rooms []
    _.each data.destinations, (destination) ->
      destination = new DestinationSearchParams()
      destination.city(destination.city)
      destination.dateFrom(moment(destination.dateFrom, 'D.M.YYYY').toDate())
      destination.dateTo(moment(destination.dateTo, 'D.M.YYYY').toDate())
      @destinations.push destination

    _.each data.rooms, (r) =>
      room = new SpRoom(@)
      room.fromObject(r)
      @rooms.push room
      
    if(data.eventId)
      @eventId = data.eventId

  url: ->
    result = 'tour/search?'
    params = []
    params.push 'start=' + @startCity()
    params.push 'return=' + @returnBack()
    _.each @destinations(), (destination, ind) =>
      if moment(destination.dateFrom())
        dateFrom = moment(destination.dateFrom()).format('D.M.YYYY')
      else
        dateFrom = '1.1.1970'
      if moment(destination.dateTo())
        dateTo = moment(destination.dateTo()).format('D.M.YYYY')
      else
        dateTo = '1.1.1970'
      params.push 'destinations[' + ind + '][city]=' + destination.city()
      params.push 'destinations[' + ind + '][dateFrom]=' + dateFrom
      params.push 'destinations[' + ind + '][dateTo]=' + dateTo

    _.each @rooms(), (room, ind) =>
      params.push.apply params, room.getParams(ind)

    if(@eventId)
      params.push 'eventId='+@eventId
    if(@orderId)
      params.push 'orderId='+@orderId
    result += params.join "&"
    return result

  hash: ->
    parts =  [@startCity(), @returnBack()]
    _.each @destinations(), (destination) ->
      parts.push destination.city()
      parts.push moment(destination.dateFrom()).format('D.M.YYYY')
      parts.push moment(destination.dateTo()).format('D.M.YYYY')
    parts.push 'rooms'
    _.each @rooms(), (room) ->
      parts.push room.getHash()

    hash = 'tours/search/' + parts.join('/') + '/'
    return hash

  GAKey: =>
    result = []
    for destination in @destinations()
      result.push destination.city()
    result.join '//'

  GAData: =>
    passangersData = "1"
    passangers = [0, 0, 0]
    for room in @rooms()
      passangers[0] += room.adults()
      passangers[1] += room.children()
      passangers[2] += room.infants()
    passangersData += ", " + passangers.join(" - ")

    result = []

    for destination in @destinations()
      stayData = passangersData + ", " + moment(destination.dateFrom()).format('D.M.YYYY') + ' - ' + moment(destination.dateTo()).format('D.M.YYYY')
      stayData += ", " + moment(destination.dateFrom()).diff(moment(), 'days') + " - " + moment(destination.dateTo()).diff(moment(destination.dateFrom()), 'days')
      result.push stayData
    result.join "//"
    

implement(SimpleSearchParams, ISearchParams)
implement(SimpleSearchParams, IRoomsContainer)  

# Used in TourPanel and search controller
class TourSearchParams
  constructor: ->
    @simpleSP = new SimpleSearchParams()
    @complexSP = new ComplexSearchParams()
    @activeSP = @simpleSP
    @complex = false
    @returnBack = @simpleSP.returnBack
  
  url: ->
    do @activeSP.url
  
  hash: ->
    do @activeSP.hash
  
  fromString: (data)->
    if data.indexOf('a/') == 0 || data.indexOf('h/') == 0
      @activeSP = @complexSP
      @complex = true
    @activeSP.fromString data
    @flightHash = @activeSP.flightHash
    @eventId = @activeSP.eventId
    @orderId = @activeSP.orderId
      
  fromObject: (data)->
    @simpleSP.fromObject data

  # Обновляем параметры после изменения тура пользователем
  fromTourData: (data)->
    @complex = true
    @activeSP = @complexSP
    @complexSP.fromTourData data

  #  removeItem: (item, event)=>
  #    event.stopPropagation()
  #    if @data().length <2
  #      return
  #    idx = @data.indexOf(item)
  #
  #    if idx ==-1
  #      return
  #    @data.splice(idx, 1)
  #    if item == @selection()
  #      @setActive @data()[0]

  GAKey: =>
    if @complex
      return
    do @simpleSP.GAKey

  GAData: =>
    if @complex
      return
    do @simpleSP.GAData

  ############
  # Костыль для ивентов
  ############

  overall: =>
    @simpleSP.overall()

  adults: =>
    @simpleSP.adults()

  children: =>
    @simpleSP.children()

  startCity: =>
    @simpleSP.startCity()


  ############
  # Методы для панели туров на главной
  #############
  
  # Добавляем точку назначения для набора панелей на главной,
  # т.е. в простые параметры
  addDestination: =>
    @simpleSP.destinations.push new DestinationSearchParams()

  # Удаляем точку назначение с набора панелей на главной,
  # т.е. всегда из простых параметров.
  removeDestination: (index, len)=>
    @simpleSP.destinations.splice(index, len)

  # Отдаем последнюю точку назначения,
  # i.e. свежедобавленную для свежесозданной панели.
  getLastDestination: =>
    _.last(@simpleSP.destinations())

  getRoomsContainer: =>
    @simpleSP

  getStartCity: =>
    @simpleSP.startCity

  urlChanged: =>
    @activeSP.urlChanged()

  # FIXME можно жить без него
  hotelId: =>
    @activeSP.hotelId()

  # FIXME можно жить без него ??!
  hotelChanged: =>
    @activeSP.hotelChanged()
