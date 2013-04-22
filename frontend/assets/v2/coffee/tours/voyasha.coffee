scaledValue = (value, max, scale, invert=false)->
  if value >= max
    return if invert then 0 else scale
  if invert
    return scale - value * scale/max
  return value * scale/max
  

# Abstact class for set of tickets
class Voyasha
  constructor: (@toursResultSet)->
    # FIXME price is enough here tbh
    if @init
      do @init
    @selected = ko.computed =>
      result = []
      for item in  @toursResultSet.data()
          if item.isAvia()
            result.push (if !item.noresults() then @handleAvia item else null)
          else
            result.push (if !item.noresults() then @handleHotels item else null)
      result
    @price = ko.computed =>
      result = 0
      for item in @selected()
        if item?
          result += item.price
      result

    @title = do @getTitle

  handleAvia: =>
    throw "Implement me"

  # Длина вектора {Звезды, Рейтинг, Близость центра, Дешевизна} и есть рейтинг отеля
  getRating: (x, maxPrice, maxDistance) ->
    if x.rating == '-'
      userRating = 0
    else
      userRating = scaledValue x.rating, 5, @RATING_WEIGHT
    stars = scaledValue x.starsNumeric, 5, @STARS_WEIGHT

    # расстояние до центра
    dCenter = scaledValue x.distanceToCenter, maxDistance, @DISTANCE_WEIGHT, true

    # разумность цены :D
    rPrice = scaledValue x.roomSets()[0].discountPrice, maxPrice, @PRICE_WEIGHT, true
    
    hotelRating = Math.sqrt(stars*stars + userRating*userRating + dCenter*dCenter + rPrice*rPrice)
    return hotelRating

  handleHotels: (item) =>
    data = item.results().data()
    maxPrice = _.reduce data, (memo,hotel)  =>
        if memo > hotel.roomSets()[0].discountPrice then memo else hotel.roomSets()[0].discountPrice
      , data[0].roomSets()[0].discountPrice
    maxDistance = _.reduce data, (memo,hotel)  =>
        if !hotel.distanceToCenter
          return memo
        if memo > hotel.distanceToCenter then memo else hotel.distanceToCenter
      , 0
      
    found = _.reduce data, (memo,hotel)  =>
        if @getRating(memo, maxPrice, maxDistance) > @getRating(hotel, maxPrice, maxDistance) then memo else hotel
      , data[0]
    result = {roomSet: found.roomSets()[0], hotel : data, price: found.roomSets()[0].discountPrice}
    return result

  choose: =>
    # we can just use selected here and still use @best @cheapest in avia handlers
    for item in  @toursResultSet.data()
      if item.isAvia()
        item.select (if item.noresults() then null else @handleAvia item)
      else
        item.select (if item.noresults() then null else @handleHotels item)
    do @toursResultSet.showOverview

class VoyashaCheapest extends Voyasha
  init: =>
    @PRICE_WEIGHT = 5
    @RATING_WEIGHT = 0
    @STARS_WEIGHT = 0
    @DISTANCE_WEIGHT = 0

  getTitle: =>
    'Самый дешевый'

  # item is TourAviaResultSet
  handleAvia: (item)=>
    _.reduce item.results().data,
      (memo, flight) ->
        if memo.price < flight.price then memo else flight
      , item.results().data[0]

class VoyashaOptima extends Voyasha
  init: =>
    @PRICE_WEIGHT = 10
    @RATING_WEIGHT = 3
    @STARS_WEIGHT = 5
    @DISTANCE_WEIGHT = 7

  getTitle: =>
    'Оптимальный вариант'

  handleAvia: (item)=>
    item.results().getFilterLessBest()
    
class VoyashaRich extends Voyasha
  init: =>
    @PRICE_WEIGHT = 5
    @RATING_WEIGHT = 3
    @STARS_WEIGHT = 5
    @DISTANCE_WEIGHT = 5

  getTitle: =>
    'Роскошный вариант'

  handleAvia: (item)=>
    data = item.results().data
    result = {'direct': data[0].directRating(), 'price': data[0].price, 'result': data[0]}
    for item in data
      if item.directRating() < result.direct
        result.direct = item.directRating()
        result.price = item.price
        result.result = item
      else if item.directRating() == result.direct
        if item.price < result.price
          result.price = item.price
          result.result = item
    return result.result
