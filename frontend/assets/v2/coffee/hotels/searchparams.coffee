class SpRoom
  constructor: () ->
    @adults = ko.observable(1).extend({integerOnly: {min: 1, max:4}})
    @children = ko.observable(0).extend({integerOnly: {min: 0, max:4}})
    @ages = ko.observableArray()

  fromList: (item) ->
    parts = item.split(':')
    @adults parts[0]
    @children parts[1]
    for i in [0..@children]
      ages.push ko.observable parts[2+i]
    @children.subscribe (newValue)=>
      if @ages().length < newValue
        for i in [0..(newValue-@ages().length-1)]
          @ages.push ko.observable 12
          console.log "$# PUSHING", i
      else if @ages().length > newValue
        @ages.splice(newValue)
    ko.processAllDeferredBindingUpdates()

  fromObject: (item) ->
    @adults +item.adultCount
    @children +item.childCount

  getHash: =>
    parts = [@adults(), @children()]
    for age in @ages()
      parts.push age
    return parts.join(':')

  getUrl: (i)=>
    # FIXME FIMXE FIMXE
    return "rooms[#{i}][adt]=" + @adults() + "&rooms[#{i}][chd]=" + @children() + "&rooms[#{i}][chdAge]=0&rooms[#{i}][cots]=0"

class HotelsSearchParams
  constructor: ->
    @city = ko.observable('')
    @checkIn = ko.observable(false)
    @checkOut = ko.observable(false)
    @rooms = ko.observableArray [new SpRoom()]

  getHash: =>
    parts =  [@city(), moment(@checkIn()).format('D.M.YYYY'), moment(@checkOut()).format('D.M.YYYY')]
    for room in @rooms()
      parts.push room.getHash()
    hash = 'hotels/search/' + parts.join('/') + '/'
    window.voyanga_debug "Generated hash for hotels search", hash
    return hash

  fromList: (data)->
    # FIXME looks too ugly to hit production, yet does not support RT
    @city data[0]
    @checkIn moment(data[1], 'D.M.YYYY').toDate()
    @checkOut moment(data[2], 'D.M.YYYY').toDate()
    @rooms.splice(0)
    rest = data[3].split('/')
    for item in rest
      if item
        r = new SpRoom()
        r.fromList(item)
        @rooms.push r

  fromObject: (data)->
    @city data.city
    @checkIn moment(data.checkIn, 'YYYY-M-D').toDate()
    @checkOut moment(data.checkIn, 'YYYY-M-D').add('days',data.duration).toDate()
    @rooms.splice(0)
    for item in data.rooms
      r = new SpRoom()
      r.fromObject(item)
      @rooms.push r

  url: =>
    result = "hotel/search?city=" + @city()
    result += '&checkIn=' + moment(@checkIn()).format('YYYY-M-D')
    result += '&duration=' + moment(@checkOut()).diff(moment(@checkIn()), 'days')
    for room, i in @rooms()
      result += '&' + room.getUrl(i)
    return result
