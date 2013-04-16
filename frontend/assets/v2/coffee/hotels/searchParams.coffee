class SpRoom
  constructor: (@parent) ->
    @adults = ko.observable(2).extend({integerOnly:
      {min: 1, max: 4}})
    @children = ko.observable(0).extend({integerOnly:
      {min: 0, max: 2}})
    @ages = ko.observableArray()
    @infants = ko.observable(0).extend({integerOnly:
      {min: 0, max: 2}})

    @adults.subscribe (newValue)=>
      if newValue + @children() > 5
        @adults 5 - @children()
      if (@parent.overall() - @adults() + newValue) > 9
        @adults 9 - @parent.overall() + @adults()

    @children.subscribe (newValue)=>
      if newValue + @adults() > 5
        newValue = 5 - @adults()
        @children newValue
      if (@parent.overall() - @children() + newValue) > 9
        @children 9 - @parent.overall() + @children()


      if @ages().length == newValue
        return
      if @ages().length < newValue
        for i in [0..(newValue - @ages().length - 1)]
          @ages.push {age: ko.observable(12).extend {integerOnly:{min: 0, max: 12}}}
      else if @ages().length > newValue
        @ages.splice(newValue)
      ko.processAllDeferredBindingUpdates()

  fromPEGObject: (item) ->
    @adults item.adults
    @children item.children
    @infants item.infants
    # FIXME: FIXME FIXME
    if @children() > 0
      for i in [0..(@children()-1)]
        @ages.push {age: ko.observable(item.ages[i]).extend {integerOnly:{min: 0, max: 12}}}

  fromObject: (item) ->
    @adults item.adt || item.adultCount
    @children item.chd  || item.childCount
    @infants item.cots
    
    if @children() > 0
      for i in [0..(@children()-1)]
        @ages.push {age: ko.observable(item.chdAges[i]).extend {integerOnly:{min: 0, max: 12}}}


  getHash: =>
    parts = [@adults(), @children(), @infants()]

    for age in @ages()
      parts.push age.age()
    return parts.join(':')

  getParams: (i)=>
    params = []
    j = 0
    for ageObj in @ages()
      params.push "rooms[#{i}][chdAges][#{j}]=" + ageObj.age()
      j++
    if !params.length
      params.push "rooms[#{i}][chdAges]=0"
    params.push "rooms[#{i}][adt]=" + @adults()
    params.push "rooms[#{i}][chd]=" + @children()
    params.push "rooms[#{i}][cots]=" + @infants()
    return params

class HotelsSearchParams extends RoomsContainerMixin
  constructor: ->
    @city = ko.observable('')
    @checkIn = ko.observable(false)
    @checkOut = ko.observable(false)
    @rooms = ko.observableArray [new SpRoom(@)]
    @hotelId = ko.observable(false)
    @urlChanged = ko.observable(false)
    @hotelChanged = ko.observable(false)
    @overall = ko.computed =>
      result = 0
      for room in @rooms()
        result += room.adults()
        result += room.children()
      return result

  hash: =>
    parts =  [@city(), moment(@checkIn()).format('D.M.YYYY'), moment(@checkOut()).format('D.M.YYYY')]
    for room in @rooms()
      parts.push room.getHash()
    hash = 'hotels/search/' + parts.join('/') + '/'
    return hash

  fromString: (data)=>
    data = PEGHashParser.parse(data,'HOTELS')
    @fromPEGObject data

  fromPEGObject: (data)=>
    beforeUrl = @url()
    hotelIdBefore = @hotelId()
    @city data.to
    @checkIn data.dateFrom
    @checkOut data.dateTo
    @hotelId(false)

    # FIXME dependency leak ?
    @rooms.splice 0
    @hotelId(false)
    if data.extra
      for pair in data.extra
        if pair.key == 'hotelId'
          @hotelId pair.value
    for room in data.rooms
      r = new SpRoom(@)
      r.fromPEGObject(room)
      @rooms.push r

    if beforeUrl == @url()
      @urlChanged(false)
      if hotelIdBefore == @hotelId()
        @hotelChanged(false)
      else
        @hotelChanged(true)
    else
      @urlChanged(true)
      @hotelChanged(false)

  fromObject: (data)=>
    @city data.city
    @checkIn moment(data.checkIn, 'YYYY-M-D').toDate()
    @checkOut moment(data.checkIn, 'YYYY-M-D').add('days', data.duration).toDate()
    @rooms.splice(0)

    for item in data.rooms
      r = new SpRoom(@)
      r.fromObject(item)
      @rooms.push r

  url: =>
    result = "hotel/search?"
    params = @getParams()
    result += params.join "&"
    return result
    
  getParams: (include_type=false)=>
    params = []
    if include_type
      params.push 'type=hotel'
    params.push 'city=' + @city()
    params.push 'checkIn=' + moment(@checkIn()).format('YYYY-M-D')
    params.push 'duration=' + moment(@checkOut()).diff(moment(@checkIn()), 'days')
    for room, i in @rooms()
      params.push.apply params, room.getParams(i)
    return params

  GAKey: =>
    @city()

  GAData: =>
    result = "1"
    passangers = [0, 0, 0]
    for room in @rooms()
      passangers[0] += room.adults()
      passangers[1] += room.children()
      passangers[2] += room.infants()
    result += ", " + passangers.join(" - ")
    result += ", " + moment(@checkIn()).format('D.M.YYYY') + ' - ' + moment(@checkOut()).format('D.M.YYYY')
    result += ", " + moment(@checkIn()).diff(moment(), 'days') + " - " + moment(@checkOut()).diff(moment(@checkIn()), 'days')
    return result

implement(HotelsSearchParams, ISearchParams)
implement(HotelsSearchParams, IRoomsContainer)
