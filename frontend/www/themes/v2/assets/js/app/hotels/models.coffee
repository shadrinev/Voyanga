STARS_VERBOSE = ['one', 'two', 'three', 'four', 'five']

class HotelFilter
  constructor: (@data) ->
    @name = 'noname'
  filter: (value) ->
    if !value
      console.log('filtered by '+@name)

class HotelNameFilter extends HotelFilter
  constructor: (hotelNames)->
    @name = 'NameFilter'
    @active = ko.observable('')

  filter: (object)->
    result = true
    if @active() != ''
      expr = new RegExp(@active(), 'ig');
      result = expr.test object.hotelName
    super result
    return result

class HotelServicesFilter extends HotelFilter
  constructor: (servicesNames)->
    @name = 'ServicesFilter'
    @services = []
    for serviceName, foo of servicesNames
      @services.push {'name':serviceName, 'active': ko.observable 0 }
    @active = ko.computed =>
      result = []
      for line in @services
        if line.active()
          result.push line.name
      return result

  reset: =>
    for line in @services
      line.active(0)

  filter: (object)->
    result = true
    _active = @active()
    if _active.length > 0
      found = false
      if object.hasHotelServices
        for serviceName in object.hotelServices
          if _active.indexOf(serviceName) != -1
            found = true
            break
      result = found
    super result
    return result

class HotelStarsFilter extends HotelFilter
  constructor: ->
    @name = 'StarsFilter'
    @stars = []
    for i in [1..5]
      @stars.push {'name':i, 'active': ko.observable 0 }
    @active = ko.computed =>
      result = []
      for line in @stars
        if line.active()
          result.push line.name
      return result

  filter: (object)->
    result = true
    _active = @active()
    if _active.length > 0
      found = false
      if _active.indexOf(object.stars) != -1
        found = true
      result = found
    super result
    return result

class Room
  constructor: (data) ->
    @name = data.showName
    @meal = data.mealName

    if typeof @meal == "undefined" || @meal == ''
      @meal = 'Не известно'
    #console.log(@meal)
    @hasMeal = (@meal != 'Без питания' && @meal != 'Не известно')

class RoomSet
  constructor: (data, duration = 1) ->
    @price = Math.ceil(data.rubPrice)
    @pricePerNight = Math.ceil(@price / duration)
    @visible = ko.observable(true)


    @rooms = []
    for room in data.rooms
      @rooms.push new Room room
#
# Stacked hotel, FIXME can we use this as roomset ?
#
class HotelResult
  constructor: (data, duration = 1) ->
    # Mix in events
    _.extend @, Backbone.Events

    @hotelName = data.hotelName
    @address = data.address
    @description = data.description
    # FIXME check if we can get diffirent photos for different rooms in same hotel
    @photos = data.images
    @numPhotos = 0
    # FIXME trollface
    @frontPhoto =
      smallUrl: 'http://upload.wikimedia.org/wikipedia/en/thumb/7/78/Trollface.svg/200px-Trollface.svg.png'
      largeUrl: 'http://ya.ru'
    if @photos && @photos.length
      @frontPhoto = @photos[0]
      @numPhotos = @photos.length

    # for popup
    @activePhoto = @frontPhoto['largeUrl']
    # FIXME check if categoryId matches star rating
    @stars = STARS_VERBOSE[data.categoryId-1]
    @rating = data.rating
    # coords
    @lat = data.latitude / 1
    @lng = data.longitude / 1
    @distanceToCenter = Math.round(data.centerDistance/1000)

    @duration = duration

    @hasHotelServices = if data.facilities then true else false
    @hotelServices = data.facilities
    @hasRoomAmenities = if data.roomAmenities then true else false
    @roomAmenities = data.roomAmenities
    @roomSets = []
    @visible = ko.observable(true)
    @push data

  push: (data) ->
    set = new RoomSet data,@duration
    if @roomSets.length == 0
      @cheapest = set.price
      @minPrice = set.pricePerNight
      @maxPrice = set.pricePerNight
    else
      @cheapest = if set.price < @cheapest then set.price else @cheapest
      @minPrice = if set.pricePerNight < @minPrice then set.pricePerNight else @minPrice
      @maxPrice = if set.pricePerNight > @maxPrice then set.pricePerNight else @maxPrice
    @roomSets.push set
    @roomSets = _.sortBy @roomSets, (entry)-> entry.price

  showPhoto: =>
    new PhotoBox(@photos)


  # FIXME copy-pasted from avia
  # Shows popup with detailed info about given result
  showDetails: =>
    # If user had clicked read-more link
    @readMoreExpanded = false
    new GenericPopup '#hotels-body-popup', @

    SizeBox('hotels-body-popup')
    ResizeBox('hotels-body-popup')
    sliderPhoto('.photo-slide-hotel')
    # FIXME explicitly call tab handler here ?
    $(".description .text").dotdotdot({watch: 'window'})

    # If we initialized google map already
    @mapInitialized = false

  showMapDetails: =>
    @showDetails()
    @showMap()


  # Click handler for map/description in popup
  showMap: (context, event) =>
    el = $('#hotels-popup-tumblr-map')
    if el.hasClass('active')
      return
    $('.place-buy .tmblr li').removeClass('active')
    el.addClass('active')
    $('.tab').hide();
    $('#hotels-popup-map').show()
    $('#boxContent').css 'height', $('#hotels-popup-map').height() + $('#hotels-popup-header1').height() + $('#hotels-popup-header2').height() + 'px'
    if ! @mapInitialized
      coords = new google.maps.LatLng(@lat, @lng)
      mapOptions =
        center: coords
        zoom: 12
        mapTypeId: google.maps.MapTypeId.ROADMAP
      map = new google.maps.Map $('#hotels-popup-gmap')[0], mapOptions
      marker = new google.maps.Marker
        position: coords
        map: map
        title: @hotelName
      @mapInitialized = true
    SizeBox 'hotels-popup-body'

  showDescription: (context, event) ->
    el = $('#hotels-popup-tumblr-description')
    if el.hasClass('active')
      return
    $('.place-buy .tmblr li').removeClass('active')
    el.addClass('active')
    $('.tab').hide();
    $('#hotels-popup-description').show()
    $(".description .text").dotdotdot({watch: 'window'})
    $('#boxContent').css 'height', 'auto'
    SizeBox 'hotels-popup-body'

  #mapTumbler: (context, event) =>
  #  el = $(event.currentTarget)
  #  if ! el.hasClass('active')
  #    var_nameBlock = el.attr('href')
  #    var_nameBlock = var_nameBlock.slice(1)
  #    $('.place-buy .tmblr li').removeClass('active')
  #    el.parent().addClass('active')
  #    $('.tab').hide();
  #    $('#'+var_nameBlock).show()

#    sliderPhoto('.photo-slide-hotel');
#    $('a.photo').click(function(e) {
#      e.preventDefault();
#      createPhotoBox(this);
#    });
#    $(".description .text").dotdotdot({watch: 'window'});


  # Click handler for read more button in popup
  readMore: (context, event)->
    el = $(event.currentTarget)
    if ! el.hasClass('active')
      var_heightCSS = el.parent().find('.text').css('height');
      var_heightCSS = Math.abs(parseInt(var_heightCSS.slice(0,-2)));
      el.parent().find('.text').attr('rel',var_heightCSS).css('height','auto');
      $(".description .text").dotdotdot({watch: 'window'});
      $(".description .text").css('overflow','visible');
      el.text('Свернуть');
      el.addClass('active');
    else
      rel = el.parent().find('.text').attr('rel');
      el.parent().find('.text').css('height', rel+'px');
      el.text('Подробнее');
      el.removeClass('active');
      $(".description .text").dotdotdot({watch: 'window'});
      $(".description .text").css('overflow','hidden');
    SizeBox('hotels-popup-body')

  smallMapUrl: =>
      base = "http://maps.googleapis.com/maps/api/staticmap?zoom=13&size=310x259&maptype=roadmap&markers=color:red%7Ccolor:red%7C"
      base += "%7C"
      base += @lat + "," + @lng
      base += "&sensor=false"
      return base



#
# Result container
# Stacks them by price and company
#
class HotelsResultSet
  constructor: (rawHotels, @searchParams) ->
    @_results = {}
    @checkIn = moment(@searchParams.checkIn)
    @checkOut = moment(@checkIn).add('days', @searchParams.duration)
    if duration == 0
      for hotel in rawHotels
        if typeof hotel.duration == 'undefined'
          checkIn = dateUtils.fromIso hotel.checkIn
          console.log checkIn
          checkOut = dateUtils.fromIso hotel.checkOut
          console.log hotel.checkOut
          console.log checkOut
          duration = checkOut.valueOf() - checkIn.valueOf()
          duration =  Math.floor(duration / (3600 * 24 * 1000))
        else
          duration = hotel.duration
        break

    @minPrice = false
    @maxPrice = false
    for hotel in rawHotels
      key = hotel.hotelId
      if @_results[key]
        @_results[key].push hotel
        @minPrice = if @_results[key].minPrice < @minPrice then @_results[key].minPrice else @minPrice
        @maxPrice = if @_results[key].maxPrice > @maxPrice then @_results[key].maxPrice else @maxPrice
      else
        result =  new HotelResult hotel, duration
        @_results[key] = result
        if @minPrice == false
          @minPrice = @_results[key].minPrice
          @maxPrice = @_results[key].maxPrice
        else
          @minPrice = if @_results[key].minPrice < @minPrice then @_results[key].minPrice else @minPrice
          @maxPrice = if @_results[key].maxPrice > @maxPrice then @_results[key].maxPrice else @maxPrice

    # We need array for knockout to work right
    @data = []
    @_services = {}
    @_names = []
    @stars = {}


    for key, result of @_results
      @data.push result
      @_names.push(result.hotelName)
      @stars[result.stars] = 1
      if result.hasHotelServices
        for service in result.hotelServices
          @_services[service] = 1

    @allFilters = {}
    @allFilters['starsFilter'] = new HotelStarsFilter()
    @allFilters['servicesFilter'] = new HotelServicesFilter(@_services)
    @allFilters['nameFilter'] = new HotelNameFilter(@_names)

    @allFiltersActive = ko.computed =>
      result = {}
      for filterName,filterObject of @allFilters
        result[filterName] = filterObject.active()
      return result

    @allFiltersActive.subscribe (value) =>
      console.log "REFILTER"

    @data = _.sortBy @data, (entry)-> entry.roomSets[0].price

# Model for Hotel info params,
# Used in infoAcion controller
class HotelInfoParams
  constructor: ->
    @cacheId = ''
    @hotelId = ''

  url: ->
    result = 'http://api.voyanga/v1/hotel/search/info/'+@cacheId+'/'+@hotelId+'/'

    return result


  key: ->
    key = @dep() + @arr() + @date
    if @rt
      key += @rt_date
    key += @adults()
    key += @children()
    key += @infants()
    return key

  getHash: ->
    # FIXME
    hash = 'avia/search/' + [@dep(), @arr(), @date, @adults(), @children(), @infants()].join('/') + '/'
    window.voyanga_debug "Generated hash for avia search", hash
    return hash


  fromList: (data)->
    @cacheId data[0]
    @hotelId data[1]
