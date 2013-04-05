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

  # FIXME remove meh
  fromList: (item) ->
    parts = item.split(':')
    @adults parts[0]
    @children parts[1]
    @infants parts[2]
    # FIXME: FIXME FIXME
    if @children() > 0
      for i in [0..(@children()-1)]
       @ages.push {age: ko.observable(parts[3 + i]).extend {integerOnly:{min: 0, max: 12}}}

  fromPEGObject: (item) ->
    @adults item.adults
    @children item.children
    @infants item.infants
    # FIXME: FIXME FIXME
    if @children() > 0
      for i in [0..(@children()-1)]
        @ages.push {age: ko.observable(item.ages[i]).extend {integerOnly:{min: 0, max: 12}}}

  fromObject: (item) ->
    @adults item.adt
    @children item.chd
    @infants item.cots
    
    if @children() > 0
      for i in [0..(@children()-1)]
        @ages.push {age: ko.observable(item.chdAges[i]).extend {integerOnly:{min: 0, max: 12}}}


  getHash: =>
    parts = [@adults(), @children(), @infants()]

    for age in @ages()
      parts.push age.age()
    return parts.join(':')

  getUrl: (i)=>
    # FIXME FIMXE FIMXE
    agesText = ''
    agesTextVals = []
    j = 0
    for ageObj in @ages()
      console.log('age', ageObj, ageObj.age())
      agesTextVals.push("rooms[#{i}][chdAges][#{j}]=" + ageObj.age())
      j++
    if(agesTextVals.length)
      agesText = agesTextVals.join('&')
    if !agesText
      agesText = "rooms[#{i}][chdAges]=0"
    return "rooms[#{i}][adt]=" + @adults() + "&rooms[#{i}][chd]=" + @children() + "&" + agesText + "&rooms[#{i}][cots]=" + @infants()

class HotelsSearchParams
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

  getHash: =>
    parts =  [@city(), moment(@checkIn()).format('D.M.YYYY'), moment(@checkOut()).format('D.M.YYYY')]
    for room in @rooms()
      parts.push room.getHash()
    hash = 'hotels/search/' + parts.join('/') + '/'
    window.voyanga_debug "Generated hash for hotels search", hash
    return hash

  fromList: (data)=>
    # FIXME looks too ugly to hit production, yet does not support RT
    beforeUrl = @url()
    hotelIdBefore = @hotelId()
    @city data[0]
    @checkIn moment(data[1], 'D.M.YYYY').toDate()
    @checkOut moment(data[2], 'D.M.YYYY').toDate()
    @rooms.splice(0)
    @hotelId(false)
    rest = data[3].split('/')
    for item in rest
      if item == 'hotelId'
        @hotelId(0)
      else
        if @hotelId() == 0
          @hotelId(item)
          break
        else
          if item
            r = new SpRoom(@)
            r.fromList(item)
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
    result = "hotel/search?city=" + @city()
    result += '&checkIn=' + moment(@checkIn()).format('YYYY-M-D')
    result += '&duration=' + moment(@checkOut()).diff(moment(@checkIn()), 'days')
    for room, i in @rooms()
      result += '&' + room.getUrl(i)
    return result

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
