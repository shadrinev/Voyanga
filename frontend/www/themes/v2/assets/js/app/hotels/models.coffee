STARS_VERBOSE = ['one', 'two', 'three', 'four', 'five']

class Room
  constructor: (data) ->
    @name = data.showName
    @meal = data.meal
    @hasMeal = (@meal != 'Без питания' && @meal != 'Не известно')

class RoomSet
  constructor: (data) ->
    @price = data.rubPrice

    @rooms = []
    for room in data.rooms
      @rooms.push new Room room
#
# Stacked hotel, FIXME can we use this as roomset ?
#
class HotelResult
  constructor: (data) ->
    # Mix in events
    _.extend @, Backbone.Events

    @hotelName = data.hotelName
    @address = data.address
    console.log data
    @description = data.description
    # FIXME check if we can get diffirent photos for different rooms in same hotel
    @photos = data.images
    @numPhotos = 0
    @frontPhoto =
      smallUrl: 'http://upload.wikimedia.org/wikipedia/en/thumb/7/78/Trollface.svg/200px-Trollface.svg.png'
      largeUrl: 'http://ya.ru'
    if @photos && @photos.length
      @frontPhoto = @photos[0]
      @numPhotos = @photos.length
    # FIXME check if categoryId matches star rating
    @stars = STARS_VERBOSE[data.categoryId-1]
    @rating = data.rating
    @roomSets = []
    @push data

  push: (data) ->
    @roomSets.push new RoomSet data

  # FIXME copy-pasted from avia

  # Shows popup with detailed info about given result
  showDetails: =>
    window.voyanga_debug "HOTELS: Setting popup result", @
    @trigger "popup", @
    $('body').prepend('<div id="popupOverlay"></div>')

    $('#hotels-body-popup').show()
    ko.processAllDeferredBindingUpdates()

    SizeBox('hotels-popup-body')
    ResizeBox('hotels-popup-body')

    $('#popupOverlay').click =>
      @closeDetails()

  # Hide popup with detailed info about given result
  closeDetails: =>
    window.voyanga_debug "Hiding popup"
    $('#hotels-body-popup').hide()
    $('#popupOverlay').remove()



#
# Result container
# Stacks them by price and company
#
class HotelsResultSet
  constructor: (rawHotels) ->
    @_results = {}

    for hotel in rawHotels
      key = hotel.hotelId
      if @_results[key]
        @_results[key].push hotel
      else
        result =  new HotelResult hotel
        @_results[key] = result
        result.on "popup", (data)=>
          @popup data

    # We need array for knockout to work right
    @data = []

    for key, result of @_results
      @data.push result

    @popup = ko.observable @data[0]
