STARS_VERBOSE = ['one', 'two', 'three', 'four', 'five']
HOTEL_SERVICE_VERBOSE = {'Сервис':'service','Спорт и отдых':'sport','Туристам':'turist','Интернет':'internet','Развлечения и досуг':'dosug','Парковка':'parkovka','Дополнительно':'dop','В отеле':'in-hotel'}
MEAL_VERBOSE = {'Американский завтрак':'Завтрак','Английский завтрак':'Завтрак','Завтрак в номер':'Завтрак','Завтрак + обед':'Завтрак и обед','Завтрак + обед + ужин':'Завтрак и обед и ужин','Завтрак + обед + ужин + напитки':'Завтрак и обед и ужин и напитки','Завтрак + ужин':'Завтрак и ужин','Континентальный завтрак':'Завтрак','Завтрак Шведский стол':'Завтрак'}

class Room
  constructor: (data) ->
    @name = data.showName
    @nameNemo = data.roomNemoName
    if !@nameNemo || data.roomName
      @nameNemo = data.roomName
    if @nameNemo != '' and typeof @nameNemo != 'undefined'
      @haveNemoName = true
    else
      @haveNemoName = false
      @nameNemo = ''

    @meal = data.meal
    if data.mealName
      @meal = data.mealName
    @last = ko.observable false
    #if data.mealBreakfast != '' and typeof data.mealBreakfast != 'undefined'
    #  @meal = data.mealBreakfast
    if typeof @meal == "undefined" || @meal == ''
      @meal = 'Не известно'
    @mealIcon = "ico-breakfast"
    if MEAL_VERBOSE[@meal]
      @meal = MEAL_VERBOSE[@meal]

    @hasMeal = (@meal != 'Без питания' && @meal != 'Не известно')
    if @hasMeal && @meal != 'Завтрак'
      @mealIcon = "ico-breakfast-dinner"
  key: =>
    return @nameNemo + @name + @meal


class RoomSet
  constructor: (data, @parent, duration = 1) ->
    @price = Math.ceil(data.rubPrice)
    # Used in tours
    @savings = 0
    @resultId = data.resultId
    @pricePerNight =  Math.ceil(@price / duration)
    @visible = ko.observable(true)
    @cancelRules = ko.observable(false)
    @cancelText = ko.computed =>
      if @cancelRules()
        result = []
        for cancelObject in @cancelRules()
          if cancelObject.charge
            nowDate = dateUtils.formatDayMonth( moment()._d )
            if nowDate == dateUtils.formatDayMonth(cancelObject.cancelDate._d)
              result.push 'Штраф взымается в размере ' + Math.ceil(cancelObject.price) + ' руб'
            else
              result.push 'Штраф взымается в размере ' + Math.ceil(cancelObject.price) + ' руб с ' + dateUtils.formatDayMonth(cancelObject.cancelDate._d)
            #console.log(resultText,cancelObject.cancelDate);
          else
            result.push 'Штраф за отмену не взымается '
        return result.join('<br>')
      else
        return 'Условия бронирования пока не известны'


    @rooms = []
    for room in data.rooms
      @rooms.push new Room room
    @rooms[(@rooms.length-1)].last(true)
    @selectedCount = ko.observable 0
    @selectedCount.subscribe (newValue)=>
      @checkCount(newValue)

    @selectText = ko.computed =>
      if !@parent.tours()
        return "Забронировать"
      if @parent.activeResultId()
        return 'Выбран'
      else
        return 'Выбрать'


  checkCount: (newValue)=>
    count = parseInt(newValue)
    if count < 0 || isNaN(count)
      @selectedCount(0)
    else
      @selectedCount(count)

  plusCount: =>
    @selectedCount(@selectedCount() + 1)

  minusCount: =>
    if @selectedCount() > 0
      @selectedCount(@selectedCount() - 1)

  key: =>
    result = @price
    for room in @rooms
      result +=room.key()
    return result

  addCancelationRules: (roomSetData)=>
    if roomSetData.cancelCharges
      roomSetData.cancelCharges.sort(
        (left,right)->
          if left.fromTimestamp < right.fromTimestamp
            return 1
          else if left.fromTimestamp > right.fromTimestamp
            return -1
          return 0
      )
      #cancelObject = roomSetData.cancelCharges.shift()
      console.log('adding cancel rules',roomSetData.cancelCharges)
      for cancelObject in roomSetData.cancelCharges
        cancelObject.cancelDate = moment.unix(cancelObject.fromTimestamp)
        console.log('date convert',cancelObject,cancelObject.fromTimestamp,cancelObject.cancelDate)
      @cancelRules(roomSetData.cancelCharges)

  showCancelationRules: (el,e)=>
    miniPopUp = '<div class="miniPopUp"></div>'
    console.log(e)
    widthThisElement = $(e.currentTarget).width()
    $('body').append(miniPopUp)
    $('.miniPopUp').html($(e.currentTarget).attr('rel')).css('left', (e.pageX - (widthThisElement / 2))+'px').css('top', (e.pageY + 10)+'px')
  hideCancelationRules: (el,ev)=>
    $('.miniPopUp').remove()
  #price: ->
  #  console.log prm
  #  console.log 'tt'
  #  return 22
#
# Stacked hotel, FIXME can we use this as roomset ?
#
class HotelResult
  constructor: (data, parent, duration, @activeHotel, hotelDatails) ->
    # Mix in events
    _.extend @, Backbone.Events
    if !hotelDatails
      hotelDatails = {}
    @tours =  parent.tours
    @hotelId = data.hotelId
    @cacheId = parent.cacheId
    @activeResultId = ko.observable 0 
    @hotelName = data.hotelName
    @address = hotelDatails.address
    @description = hotelDatails.description
    @limitDesc = Utils.limitTextLenght(@description,195)
    @showMoreDesc = ko.observable(true)
    @showMoreDescText = ko.computed =>
      if @showMoreDesc()
        return 'Подробнее'
      else
        return 'Свернуть'
    # FIXME check if we can get diffirent photos for different rooms in same hotel
    @photos = hotelDatails.images
    @numPhotos = 0
    @parent = parent
    @checkInTime = hotelDatails.earliestCheckInTime
    if @checkInTime
      @checkInTime = @checkInTime.substr(0,@checkInTime.length - 3)
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
    @starsNumeric = data.categoryId
    @stars = STARS_VERBOSE[@starsNumeric-1]
    @rating = data.rating
    if @rating == '-'
      @rating = 0
    @ratingName = ''
    if 0 <= @rating < 2
      @ratingName = "рейтинг<br>отеля"
    else if 2 <= @rating < 2.5
      @ratingName = "средний<br>отель"
    else if 2.5 <= @rating < 3.5
      @ratingName = "неплохой<br>отель"
    else if 3.5 <= @rating < 4
      @ratingName = "хороший<br>отель"
    else if 4 <= @rating < 4.5
      @ratingName = "очень хороший<br>отель"
    else if 4.5 <= @rating <= 5
      @ratingName = "великолепный<br>отель"
    # coords
    @lat = hotelDatails.latitude / 1
    @lng = hotelDatails.longitude / 1
    @distanceToCenter = Math.ceil(data.centerDistance/1000)
    if @distanceToCenter > 30
      @distanceToCenter = 30

    @duration = duration
    console.log('duration:'+duration)
    @haveFullInfo = ko.observable false

    @selectText = ko.computed =>
      if !@tours()
        return "Забронировать"
      if @isActive()
        return 'Выбран'
      else
        return 'Выбрать'

    @hasHotelServices = if hotelDatails.hotelServices then true else false
    @hotelServices = hotelDatails.hotelServices
    @hasHotelGroupServices = if hotelDatails.hotelGroupServices then true else false
    @hotelGroupServices = []
    if hotelDatails.hotelGroupServices
      for groupName,elements of hotelDatails.hotelGroupServices
        @hotelGroupServices.push {groupName: groupName,elements: elements,groupIcon: HOTEL_SERVICE_VERBOSE[groupName]}
    #if @hasHotelServices
    #  for service in @hotelServices
    #    if service == 'Фитнесс-центр'
    #      service = 'Фитнесс'
    @hasRoomAmenities = if hotelDatails.roomAmenities then true else false
    @roomAmenities = hotelDatails.roomAmenities
    @roomSets = ko.observableArray([])
    #@roomSets = []
    console.log(@roomSets())
    @visible = ko.observable(true)
    @wordDays = @parent.wordDays
    @visibleRoomSets = ko.computed =>
      result = []
      for roomSet in @roomSets()
        if roomSet.visible()
          result.push roomSet
      if result.length > 0
        window.voyanga_debug('all results for hotel'.result)
      else window.voyanga_debug('all results for hotel zero')
      return result
    @isShowAll = ko.observable(false)
    @showAllText = ko.computed =>
      if @isShowAll()
        return 'Свернуть все результаты'
      else
        return 'Посмотреть все результаты'
    @push data

  push: (data) ->
    set = new RoomSet data, @, @duration
    set.resultId = data.resultId
    if @roomSets().length == 0
      @cheapest = set.price
      @cheapestSet = set  
      @minPrice = set.pricePerNight
      @maxPrice = set.pricePerNight
    else
      @cheapest = if set.price < @cheapest then set.price else @cheapest
      @cheapestSet = if set.price < @cheapest then set else @cheapestSet 
      @minPrice = if set.pricePerNight < @minPrice then set.pricePerNight else @minPrice
      @maxPrice = if set.pricePerNight > @maxPrice then set.pricePerNight else @maxPrice
    @roomSets.push set
    #@roomSets = _.sortBy @roomSets, (entry)-> entry.price
    @roomSets.sort (left,right)=>
      if left.price > right.price
        return 1
      else if left.price < right.price
        return -1
      return 0

  isActive: =>
    if @activeHotel
      return @activeHotel() == @hotelId
    return false

  showPhoto: (fp,ev)=>
    #window.voyanga_debug('click info',fp,ev)
    #console.log(ev.target)
    #console.log($(ev.target).data())
    ind = $(ev.currentTarget).data('photo-index')
    #console.log(ind)
    if !ind
      ind = 0
    #console.log(ind)
    new PhotoBox(@photos,@hotelName,@stars,ind)

  showAllResults: (data,event)->
    #console.log(event)
    if @isShowAll()
      $(event.currentTarget).parent().parent().find('.hidden-roomSets').hide('fast')
      #$(event.currentTarget).parent().hide()
      @isShowAll(false)
    else
      $(event.currentTarget).parent().parent().find('.hidden-roomSets').show('fast')
      #$(event.currentTarget).parent().hide()
      @isShowAll(true)

  # FIXME copy-pasted from avia
  # Shows popup with detailed info about given result
  showDetails: (data, event)=>
    # If user had clicked read-more link
    @oldPageTop = $('html').scrollTop() | $('body').scrollTop()
    @readMoreExpanded = false
    @activePopup = new GenericPopup '#hotels-body-popup', @
    SizeBox('hotels-body-popup')
    ResizeBox('hotels-body-popup')
    #sliderPhoto('.photo-slide-hotel')
    # FIXME explicitly call tab handler here ?
    #$(".description .text").dotdotdot({watch: 'window'})

    # If we initialized google map already
    @mapInitialized = false

  selectFromPopup: (hotel, event) =>
    @activePopup.close()
    hotel.off 'back'
    hotel.on 'back', =>
      window.app.render({results: ko.observable(hotel.parent)}, 'results')
      window.setTimeout(
        ->
          Utils.scrollTo(hotel.oldPageTop,false)
          console.log(hotel.oldPageTop)
        , 50
      )

    hotel.getFullInfo()
    window.app.render(hotel, 'info-template')
    Utils.scrollTo('#content',false)


  showMapDetails: (data, event)=>
    @showDetails(data, event)
    @showMap()

  # FIXME refactor
  showMapInfo: (context, event)=>
    # FIXME FIXME FIMXE why this code navigates if we wont stop default?
    event.preventDefault()
    el = $('#hotel-info-tumblr-map')
    if el.hasClass('active')
      return
    $('.place-buy .tmblr li').removeClass('active')
    el.addClass('active')
    $('#descr').hide()
    $('#map').show()
    if ! @mapInitialized
      coords = new google.maps.LatLng(@lat, @lng)
      mapOptions =
        center: coords
        zoom: 12
        mapTypeId: google.maps.MapTypeId.ROADMAP
      map = new google.maps.Map $('#hotel-info-gmap')[0], mapOptions
      marker = new google.maps.Marker
        position: coords
        map: map
        title: @hotelName
      @mapInitialized = true

  showDescriptionInfo: (context, event) ->
    el = $('#hotel-info-tumblr-description')
    if el.hasClass('active')
      return
    $('.place-buy .tmblr li').removeClass('active')
    el.addClass('active')
    $('#map').hide();
    $('#descr').show()
    #$(".description .text").dotdotdot({watch: 'window'})
    $('#boxContent').css 'height', 'auto'

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
    #$(".description .text").dotdotdot({watch: 'window'})
    $('#boxContent').css 'height', 'auto'
    SizeBox 'hotels-popup-body'

  initFullInfo: =>
    @roomCombinations = ko.observableArray([])
    @combinedPrice = ko.computed =>
      res = 0
      for roomSet in @roomCombinations()
        if roomSet.selectedCount()
          res += roomSet.selectedCount()*roomSet.price
      return res


    @combinedButtonLabel = ko.computed =>
      if @combinedPrice() > 0
        return @selectText()
      else
        return 'Не выбраны номера'

  getFullInfo: ()=>
    if !@haveFullInfo()
      api = new HotelsAPI
      url = 'hotel/search/info/?hotelId='+@hotelId
      url += '&cacheId='+@parent.cacheId
      console.log @parent.cacheId
      api.search(
        url,
        (data)=>
          #adding info to elements
          window.voyanga_debug 'searchInfo',data
          if !data.hotel
            return false
          @initFullInfo()
          for ind,roomSet of data.hotel.details
            set = new RoomSet roomSet, @, @duration
            @roomCombinations.push set
          cancelObjs = {}
          for ind,roomSet of data.hotel.oldHotels
            key = roomSet.resultId
            cancelObjs[key] = roomSet
          console.log(cancelObjs)
          for roomSet in @roomSets()
            key = roomSet.resultId
            if cancelObjs[key]
              roomSet.addCancelationRules(cancelObjs[key])
            else
              console.log('not found result with key',key)

          @roomMixed = ko.computed =>
            resultsObj = {}
            for roomSet in @roomSets()
              key = roomSet.key()
              if typeof resultsObj[key] == 'undefined'
                resultsObj[key] = roomSet

            for roomSet in @roomCombinations()
              key = roomSet.key()
              if typeof resultsObj[key] == 'undefined'
                resultsObj[key] = roomSet

            result = []
            for key,roomSet of resultsObj
              result.push roomSet
            return result
          @haveFullInfo(true)
          console.log(@roomCombinations())
        , false
      )

  combinationClick: =>
    console.log 'combinati data = _.filter @data(), (el) -> el.visible()on click'

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
    text_el = el.parent().find('.text')
    if @showMoreDesc()
      text_el.find('.endDesc').fadeIn(
        'fast',
        ()->
          text_el.find('.endDesc').css('display','inline')
      )
      @showMoreDesc(false)
    else
      text_el.find('.endDesc').fadeOut(
        'fast'
      )
      @showMoreDesc(true)

    #if ! el.hasClass('active')
      #var_heightCSS = el.parent().find('.text').css('height');
      #var_heightCSS = Math.abs(parseInt(var_heightCSS.slice(0,-2)));
      #text_el.attr('rel',var_heightCSS).css('height','auto');
      #text_el.dotdotdot({watch: 'window'});
      #el.text('Свернуть');
      #el.addClass('active');
    #else
      #rel = el.parent().find('.text').attr('rel');
      #text_el.css('height', rel+'px');
      #el.text('Подробнее');
      #el.removeClass('active');
      #text_el.dotdotdot({watch: 'window'});
    #FIXME should not be called on details page
    SizeBox('hotels-popup-body')

  # click handler, notify parent container that we want to go back
  back: =>
    @trigger 'back'

  select: (room) =>
    # it is actually cheapest click
    console.log room
    if room.roomSets
      room = room.roomSets()[0]
      Utils.scrollTo('.info-trip')
      return
    if @tours()
      @activeResultId room.resultId
      @trigger 'select', {roomSet: room, hotel: @}
    else
      result = {}
      result.type = 'hotel'
      result.searchId = @cacheId
      # FIXME FIXME FXIME
      result.searchKey = room.resultId
      result.adults = 0
      result.age = false
      result.cots = 0
      for room in @parent.rawSP.rooms
        result.adults += room.adultCount*1
        # FIXME looks like this could be array
        if room.childAge
          result.age = room.childAgeage
      
        result.cots += room.cots*1
      Utils.toBuySubmit [result]


  smallMapUrl: =>
      base = "http://maps.googleapis.com/maps/api/staticmap?zoom=13&size=310x259&maptype=roadmap&markers=icon:http://test.voyanga.com/themes/v2/images/pin1.png%7Ccolor:red%7Ccolor:red%7C"
      base += "%7C"
      base += @lat + "," + @lng
      base += "&sensor=false"
      return base



#
# Result container
# Stacks them by price and company
#
class HotelsResultSet
  constructor: (rawData, @searchParams, @activeHotel) ->
    @_results = {}
    # FIXME FIXME FIXEM
    @rawSP = @searchParams
    @cacheId = @searchParams.cacheId
    @tours = ko.observable false
    @checkIn = moment(@searchParams.checkIn)
    @checkOut = moment(@checkIn).add('days', @searchParams.duration)
    window.voyanga_debug('checkOut',@checkOut)
    @city = @searchParams.cityFull
    if @searchParams.duration
      duration = @searchParams.duration
    if duration == 0 || typeof duration == 'undefined'
      for hotel in rawData.hotels
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
          console.log('yes set')
        break
    @wordDays = Utils.wordAfterNum(duration,'день','дня','дней')
    @fullMapInitialized = false
    @showFullMap = ko.observable false

    @minPrice = false
    @maxPrice = false
    for hotel in rawData.hotels
      key = hotel.hotelId
      if @_results[key]
        @_results[key].push hotel
        @minPrice = if @_results[key].minPrice < @minPrice then @_results[key].minPrice else @minPrice
        @maxPrice = if @_results[key].maxPrice > @maxPrice then @_results[key].maxPrice else @maxPrice
      else
        #hotelsDetails = false
        #if
        result =  new HotelResult hotel, @, duration, @activeHotel, rawData.hotelsDetails[key+'d']
        @_results[key] = result
        if @minPrice == false
          @minPrice = @_results[key].minPrice
          @maxPrice = @_results[key].maxPrice
        else
          @minPrice = if @_results[key].minPrice < @minPrice then @_results[key].minPrice else @minPrice
          @maxPrice = if @_results[key].maxPrice > @maxPrice then @_results[key].maxPrice else @maxPrice

    # We need array for knockout to work right
    #@data = []
    @data = ko.observableArray()
    @showParts = ko.observable 1
    @showLimit = 20
    @sortBy = ko.observable( 'minPrice')
    @resultsForRender = ko.computed =>
      limit = @showParts() * @showLimit
      results = []
      for result in @data()
        if result.visible()
          results.push result
          limit--
        if limit <= 0
          break
      #if @sortBy() == 'minPrice'
      #  results = _.sortBy results, (el)-> el.minPrice
      #else
      #  results = _.sortBy results, (el)-> el.rating

      return results
    @numResults = ko.observable 0
    @filtersConfig = false
    @pagesLoad = @showParts()
    @toursOpened = false
    window.hotelsScrollCallback = (ev)=>
      @checkShowMore(ev)



    for key, result of @_results
      if result.numPhotos
        @data.push result



    @sortByPriceClass = ko.computed =>
      ret = 'hotel-sort-by-item'
      if @sortBy() == 'minPrice'
        ret += ' active'
      return ret

    @sortByRatingClass = ko.computed =>
      ret = 'hotel-sort-by-item'
      if @sortBy() == 'rating'
        ret += ' active'
      return ret

    @data.sort (left, right)->
      if left.minPrice < right.minPrice
        return -1
      if left.minPrice > right.minPrice
        return  1
      return 0


  select: (hotel, event) =>
    window.voyanga_debug ' i wonna get hotel for you',hotel
    hotel.oldPageTop = $("html").scrollTop() | $("body").scrollTop()
    hotel.off 'back'
    hotel.on 'back', =>
      window.app.render({results: ko.observable(@)}, 'results')
      window.setTimeout(
        ->
          Utils.scrollTo(hotel.oldPageTop,false)
          console.log(hotel.oldPageTop)
        , 50
      )

    hotel.getFullInfo()
    window.app.render(hotel, 'info-template')
    Utils.scrollTo('#content',false)

  showFullMapFunc: =>
    console.log('show full map')
    @showFullMap(true)
    $('#all-hotels-results').hide()
    $('#all-hotels-map').show()
    mapAllPageView()
    center = new google.maps.LatLng(@city.latitude, @city.longitude);
    options = {'zoom': 10,'center': center,'mapTypeId': google.maps.MapTypeId.ROADMAP}
    @fullMapInitialized = false
    @mapCluster = null


    if !@fullMapInitialized
      @gAllMap = new google.maps.Map($('#all-hotels-map')[0],options)
      @markerImage = new google.maps.MarkerImage('/themes/v2/images/pin1.png',new google.maps.Size(31, 31));
      @markerImageHover = new google.maps.MarkerImage('/themes/v2/images/pin2.png',new google.maps.Size(31, 31));
      @gMapInfoWin = new google.maps.InfoWindow()
      @clusterStyle = [
        {url: '/themes/v2/images/cluster_one.png',
        height: 43,
        width: 31,
        anchor: [7, 0],
        textColor: '#000',
        textSize: 18
        },
        {url: '/themes/v2/images/cluster_two.png',
        height: 54,
        width: 39,
        anchor: [11, 0],
        textColor: '#000',
        textSize: 18
        },
        {url: '/themes/v2/images/cluster_three.png',
        height: 65,
        width: 47,
        anchor: [15, 0],
        textColor: '#000',
        textSize: 18
        }
      ]
    else
      for gMarker in @gMarkers
        gMarker.setMap(null)
      @mapCluster.clearMarkers()
      if($('#all-hotels-map').html().length < 5)
        @gAllMap = new google.maps.Map($('#all-hotels-map')[0],options)
        @fullMapInitialized = false
        @mapCluster = null
      @gAllMap.setCenter(center)


      #addMarkers(markers:Array.<google.maps.Marker>, opt_nodraw:boolean)




    @gMarkers = []

    i = 0
    for hotel in @data()
      if hotel.visible()
        console.log('add hotel marker',hotel.lat, hotel.lng)
        gMarker = new google.maps.Marker({
          position: new google.maps.LatLng(hotel.lat, hotel.lng),
          map: @gAllMap,
          icon: @markerImage,
          draggable: false
        })
        hotel.gMarker = gMarker
        google.maps.event.addListener(
          gMarker,
          'mouseover',
          ((hotel)=>
            return (ev)=>
              @gMapPointShowWin(ev,hotel))(hotel)
        )
        google.maps.event.addListener(
          gMarker,
          'mouseout',
          ((hotel)=>
            return (ev)=>
              @gMapPointHideWin(ev,hotel))(hotel)
        )
        google.maps.event.addListener(
          gMarker,
          'click',
          ((hotel)=>
            return (ev)=>
              @gMapPointClick(ev,hotel))(hotel)
        )
        @gMarkers.push gMarker
        #console.log 'mm',gMarker
        i++
        #if i > 5
        #  break;

      if !@fullMapInitialized
        @mapCluster = new MarkerClusterer(@gAllMap,@gMarkers,{styles: @clusterStyle})
        @fullMapInitialized = true
      else
        @mapCluster.addMarkers(@gMarkers)
    console.log(@gAllMap)
    console.log(@gMapInfoWin)

  hideFullMap: =>
    console.log('hideFullMap')
    $('#all-hotels-results').show()
    $('#all-hotels-map').hide()
    @showFullMap(false)

  gMapPointShowWin: (event,hotel) =>
    div = '<div class="hotelMapInfo"><div class="hotelMapImage"><img src="'+hotel.frontPhoto.largeUrl+'"></div><div class="stars '+hotel.stars+'"></div><div class="hotelMapName">'+hotel.hotelName+'</div><div class="mapPriceDiv">от <div class="mapPriceValue">'+hotel.minPrice+'</div> р/ночь</div></div>'
    @gMapInfoWin.setContent(div);
    console.log(div);
    @gMapInfoWin.setPosition(event.latLng)
    @gMapInfoWin.open(@gAllMap)
    $(@gAllMap.n.panes.floatShadow).children().remove()
    mainDiv = $(@gMapInfoWin.j[0].b.d).attr('id','gMapInfoDiv')
    window.setTimeout(
      =>
        wrp = $(@gMapInfoWin.j[0].b.n)
        wrp.find('>div:first-child').css('display','none')
        $(@gMapInfoWin.j[0].b.l).attr('id','infoWrapperParentDiv').css('overflow','inherit')
        $(@gMapInfoWin.j[0].b.e).attr('id','infoWrapperDiv').css('overflow','inherit')
        #console.log(@gMapInfoWin.j[0].b)
      , 100
    )
    hotel.gMarker.setIcon(@markerImageHover)


  gMapPointHideWin: (event,hotel) =>
    hotel.gMarker.setIcon(@markerImage)
    #@gMapInfoWin.close()

  gMapPointClick: (event,hotel) =>
    @hideFullMap()
    @select(hotel)
    console.log('gMapEventClick',event,hotel)

  selectFromPopup: (hotel, event) =>
    hotel.activePopup.close()
    hotel.off 'back'
    hotel.on 'back', =>
      window.app.render({results: ko.observable(hotel.parent)}, 'results')
      window.setTimeout(
        ->
          Utils.scrollTo(hotel.oldPageTop,false)
          console.log(hotel.oldPageTop)
        , 50
      )

    hotel.getFullInfo()
    window.app.render(hotel, 'info-template')
    Utils.scrollTo('#content',false)

  getDateInterval: =>
    dateUtils.formatDayMonthInterval(@checkIn._d,@checkOut._d)

  showMoreResults: =>
    if @numResults() > (@showParts() * @showLimit)
      @showParts(@showParts()+1)

  checkShowMore: (ev)=>
    posTop = $('html').scrollTop() || $('body').scrollTop()
    fullHeight = $('html')[0].scrollHeight || $('body')[0].scrollHeight
    winHeight = $(window).height()
    if((fullHeight - (posTop+winHeight)) < 2)
      if (window.app.activeView() == 'hotels-results') || (window.app.activeView() == 'tours-results' && window.app.activeModuleInstance().innerTemplate == 'hotels-results')
        @showMoreResults()



  sortByPrice:  =>
    if @sortBy() != 'minPrice'
      @sortBy('minPrice')
      @data.sort (left, right)->
        if left.minPrice < right.minPrice
          return -1
        if left.minPrice > right.minPrice
          return  1
        return 0
      @showParts 1
      #ko.processAllDeferredBindingUpdates()

  sortByRating:  =>
    if @sortBy() != 'rating'
      @sortBy('rating')
      @data.sort (left, right)->
        if left.rating > right.rating
          return -1
        if left.rating < right.rating
          return  1
        return 0
      @showParts 1
      #console.log(@data())
      #ko.processAllDeferredBindingUpdates()

  selectHotel: (hotel, event) =>
    @select(hotel, event)

  postInit: =>
    @filters = new HotelFiltersT @

  postFilters: =>
    console.log 'post filters'
    data = _.filter @data(), (el) -> el.visible()
    @numResults data.length
    if !@pagesLoad
      @showParts 1
    else
      @showParts @pagesLoad

    console.log(@data)

    window.setTimeout(
      =>
        ifHeightMinAllBody()
        scrolShowFilter()
        if window.app.activeView() == 'hotels-results'
          Utils.scrollTo('#content')
        else if @toursOpened && @tours() && @filtersConfig
          kb = true
          #if $('.hotels-tickets .btn-cost.selected').parent().parent().parent().parent().length
            #Utils.scrollTo($('.hotels-tickets .btn-cost.selected').parent().parent().parent().parent())

        else
          Utils.scrollTo(0,false)
        if @fullMapShow
          @showFullMapFunc()

        @toursOpened = false
      , 50
    )
    #ko.processAllDeferredBindingUpdates()
    # FIXME
    #ResizeAvia()

