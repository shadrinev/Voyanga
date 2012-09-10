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
    @address = "Урюпинкс, 92-120"
    @description = """Foo bar description.
Foo bar description. Foo bar description. Foo bar description.
Foo bar description. Foo bar description. Foo bar description.
    """
    # FIXME check if categoryId matches star rating
    @stars = STARS_VERBOSE[data.categoryId-1]
    @rating = data.rating
    @roomSets = []
    @push data

  push: (data) ->
    @roomSets.push new RoomSet data
    # ololo


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
    # We need array for knockout to work right
    @data = []

    for key, result of @_results
      @data.push result

