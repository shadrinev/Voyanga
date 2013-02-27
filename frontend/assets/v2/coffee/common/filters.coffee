# Base class for search results filter
class Filter

  # filter given item, return true or false where true == item is good enough for given filter
  filter: (item) ->
    throw "override me"

  resetLimits: (item) ->

  updateLimits: (item) ->

  # Bullet proof observable/wrapper function/property getter
  get: (item, key) ->
    value = ko.utils.unwrapObservable item[key]
    if (typeof value) == 'function'
      value = value.apply(item)
    return value

  getConfig: =>
    return @selection()

  setConfig: (value)=>
    @selection(value)

    

class TimeFilter extends Filter
  constructor: (@key)->
    # so we can query limits better
    @limits = ko.rangeObservable 1440, 0
    @selection = ko.rangeObservable 0,1440
    @element = false
    # Mix in events
    _.extend @, Backbone.Events

  filter:  (result)=>
    Utils.inRange @get(result,@key), @selection()


  updateLimits: (item)->
    value = @get(item, @key)
    limits = @limits()
    if value < limits.from
      limits.from = value
    if value > limits.to
      limits.to = value
    # FIXME hardcofe KOSTIL
    @limits(limits.from+';'+limits.to)

  setConfig: (value)=>
    @selection(value.from + ';'+ value.to)
    if @element
      @element.jslider('value',value.from,value.to)

class PriceFilter extends Filter
  constructor: (@key)->
    # so we can query limits better
    @limits = ko.rangeObservable 999000, 0
    @selection = ko.rangeObservable 0,999000
    @element = false

    # Mix in events
    _.extend @, Backbone.Events

  filter:  (result)=>
    Utils.inRange @get(result,@key), @selection()

  updateLimits: (item)->
    value = @get(item, @key)
    limits = @limits()
    if value < limits.from
      limits.from = value
    if value > limits.to
      limits.to = value
    # FIXME hardcore KOSTIL
    @limits(limits.from+';'+limits.to)

  setConfig: (value)=>
    @selection(value.from + ';'+ value.to)
    if @element
      @element.jslider('value',value.from,value.to)

class DistancesFilter extends Filter
  constructor: (@key)->
    # so we can query limits better
    @limits = ko.rangeObservable 999000, 0
    @selection = ko.observable 999000
    @element = false

    # Mix in events
    _.extend @, Backbone.Events

  filter:  (result)=>
    return @get(result,@key) <= @selection()
    #Utils.inRange result[@key](), @selection()

  updateLimits: (item)->
    value = @get(item, @key)
    limits = @limits()
    if value < limits.from
      limits.from = value
    if value > limits.to
      limits.to = value
    #if limits.to > 20
    #  console.log item
    # FIXME hardcore KOSTIL
    @limits(limits.from+';'+limits.to)

  setConfig: (value)=>
    @selection(value)
    if @element
      @element.jslider('value',value)

class ListFilter extends Filter
  constructor: (@keys, @caption, @moreLabel)->
    # observable cuz we add records later
    @options = ko.observableArray()
    @_known = {}

    # should we show this filter in interface
    @active = ko.computed =>
      @options().length > 1

    @selection = ko.computed =>
      result = []
      for item in @options()
        if item.checked()
          result.push item.key
      return result

    # Mix in events
    _.extend @, Backbone.Events

  addOption: (value)->
    @_known[value]=1
    @options.remove (item)-> return item.key == value
    @options.unshift {key: value, checked: ko.observable 0}

  updateLimits: (item)->
    for key in @keys
      propValue = @get(item, key)
      if typeof propValue == 'undefined'
        continue
      else if typeof propValue != 'object'
        values = []
        values.push propValue
      else
        values = propValue
      if values
        for value in values
          if @_known[value]
            continue
          @_known[value]=1
          @options.push {key: value, checked: ko.observable 0}
          @options.sort (left, right) ->
            if left.key == right.key
              return 0
            if left.key > right.key
              return 1
            return -1


  filter: (result)=>
    if @selection().length == 0
      return true
    console.log('servFilters',@selection())

    for key in @keys
      propValue = @get(result,key)
      console.log('servFiltersProp',propValue)
      if typeof propValue != 'object'
        if @selection().indexOf(propValue) < 0
          return false
      else
        if propValue
          values = propValue
          find = true
          for value in @selection()
            find = find && (propValue.indexOf(value) >= 0)

          return find
        else
          return false
    return true

  # reset filter. click handler.
  reset: =>
    for item in @options()
      item.checked false

  # expand availiable filter items, click handler
  showMore: (context, event)=>
    el = $(event.currentTarget)
    # NOT GUILTY
    div = el.parent().parent().find('.more-filters')
  
    if !(div.css('display')=='none')
      btnText = el.text el.text().replace("Скрыть","Все")
      # Update scroll pane state.
      # FIXME decouple it somehow
      div.hide('fast', reInitJScrollPane)
    else
      btnText = el.text el.text().replace("Все","Скрыть")
      # Update scroll pane state.
      # FIXME decouple it somehow
      div.show('fast', reInitJScrollPane)

  getConfig: =>
    result = {}
    for item in @options()
      result[item.key] = item.checked()
    return result

  setConfig: (value)=>
    for item in @options()
      item.checked(value[item.key])

class StarOption
  constructor: (@key)->
    @starName = STARS_VERBOSE[@key-1]
    @checked = ko.observable 0
    @cls = ko.computed =>
      if @checked()
        return 'active'
      return ''

class StarsFilter extends Filter
  constructor: (@keys, @caption, @moreLabel)->
    # observable cuz we add records later
    @options = ko.observableArray()
    for i in [1..5]
      #option = {key: i, starName: STARS_VERBOSE[i-1], checked: ko.observable 0  }

      @options.push new StarOption i

    # should we show this filter in interface
    @active = ko.computed =>
      @options().length > 1

    @selection = ko.computed =>
      result = []
      for item in @options()
        if item.checked()
          result.push item.starName
      return result

    # Mix in events
    _.extend @, Backbone.Events

  updateLimits: (item)->


  filter: (result)=>
    if @selection().length == 0
      return true
    for key in @keys
      propValue = @get(result,key)
      console.log(@selection())
      console.log(propValue)
      if @selection().indexOf(propValue) < 0
        return false
    return true

  starClick: ()->
    console.log(this)
    if !$(this).hasClass('active')
        $(this).addClass('active')
    else
      $(this).removeClass('active')

  getConfig: =>
    result = {}
    for item in @options()
      result[item.starName] = item.checked()
    return result

  setConfig: (value)=>
    for item in @options()
      item.checked(value[item.starName])


class MaxStopoverFilter extends Filter
  constructor: (@value, @field='stopoverLength')->
    @selection = ko.observable 0

  filter: (item) =>
    if @selection()
      return @get(item, @field) <= (60*60)*@value
    return true

class OnlyDirectFilter extends Filter
  constructor: ->
    @selection = ko.observable 0

  filter: (item) =>
    if +@selection()
      return item.direct
    return true

class ServiceClassFilter extends Filter
  constructor: ->
    @selection = ko.observable 'A'

  filter: (item) =>
    lit = @selection()
    if lit == 'A'
      return item.serviceClass == 'E'
    else
      return (item.serviceClass == 'B' || item.serviceClass == 'F')

class TextFilter extends Filter
  constructor: (@key,@caption)->
    @selection = ko.observable ''
    @updateTimeout = null


  filter: (item) =>
    lit = @selection()
    result = true
    if lit != ''
      lit = lit.replace(/([\\\(\)\{\}\[\]\.\*\?\^\$\|\+\-])/g,'\\$1')
      expr = new RegExp(lit, 'ig');
      result = expr.test item[@key]
    return result

  updateResults: =>
    #ko.processAllDeferredBindingUpdates()
    @updateTimeout = null

  keyDown: =>
    if @updateTimeout != null
      window.clearTimeout(@updateTimeout)
      @updateTimeout = window.setTimeout(
        ()=>
          @updateResults()
        , 1000
      )

# FIXME write comments
class AviaFiltersT
  constructor: (@results)->
    @template = 'avia-filters'
    @rt = @results.roundTrip

    # Fixme stopover filter will cause filter method to run twice!
    # onlyDirect should actually be result filter
    @voyageFilters = ['departure', 'shortStopover', 'irrelevantlyLong', 'onlyDirect']
    @rtVoyageFilters = ['rtDeparture', 'shortStopover', 'irrelevantlyLong','onlyDirect']
    @resultFilters = ['departureAirport', 'arrivalAirport', 'airline', 'serviceClass']
    
    @departure = new TimeFilter('departureTimeNumeric')
    if @rt
      @rtDeparture = new TimeFilter('departureTimeNumeric')


    fields = if @rt then ['departureAirport','rtArrivalAirport'] else ['departureAirport']
    @departureAirport = new ListFilter(fields, @results.departureCity, 'Все аэропорты')
    fields = if @rt then ['arrivalAirport','rtDepartureAirport'] else ['arrivalAirport']
    @arrivalAirport = new ListFilter(fields, @results.arrivalCity, 'Все аэропорты')
    @airline = new ListFilter(['airlineName'], 'Авиакомпании', 'Все авиакомпании')
    @shortStopover = new MaxStopoverFilter(2.5)
    @irrelevantlyLong = new MaxStopoverFilter(30, 'maxStopoverLength')
    @irrelevantlyLong.selection 1
    @onlyDirect = new OnlyDirectFilter()
    @serviceClass = new ServiceClassFilter()

    # Saves us from multiple @filter calls on initial load
    @refilter = (ko.computed =>
      for key in @resultFilters
        @[key].selection()
      for key in @voyageFilters
        @[key].selection()
      if @rt
        for key in @rtVoyageFilters
          @[key].selection()
    ).extend {throttle: 50}
    @refilter.subscribe @filter
      

    # FIXME looks ugly
    @iterate @updateLimitsResult, @updateLimitsVoyage, @updateLimitsBackVoyage

  updateLimitsResult: (result) =>
    @runFiltersFunc result, @resultFilters, 'updateLimits'

  updateLimitsVoyage: (voyage) =>
    visible = true
    @runFiltersFunc voyage, @voyageFilters, 'updateLimits'

  updateLimitsBackVoyage: (backVoyage) =>    
    @runFiltersFunc backVoyage, @rtVoyageFilters, 'updateLimits'


  #FIXME knockout shoud handle it itself for scalar types need to check
  setVisibleIfChanged:(item, visible) ->
    if item.visible() == visible
      return
    item.visible(visible)

  filterResult: (result) =>
    @runFilters result, @resultFilters

  filterVoyage: (voyage) =>
    @runFilters voyage, @voyageFilters

  filterBackVoyage: (backVoyage) =>    
    @runFilters backVoyage, @rtVoyageFilters

  # Runs given filter against given item
  runFilters:(item, filterSet) =>
    visible = true
    for filter_key in filterSet
      visible = visible && @[filter_key].filter(item)
      if !visible
        break
    @setVisibleIfChanged(item, visible)

  runFiltersFunc:(item, filterSet, methodName) =>
    for filter_key in filterSet
      @[filter_key][methodName](item)

  filter: =>
    @iterate @filterResult, @filterVoyage, @filterBackVoyage
    #ko.processAllDeferredBindingUpdates()
    scrollValue('avia', {})

  iterate: (onResult, onVoyage, onBackVoyage) =>
    for result in @results.data
      onResult(result)
      for voyage in result.voyages
        onVoyage(voyage)
        if @rt
          for backVoyage in voyage._backVoyages
            onBackVoyage(backVoyage)
          voyage.chooseActive()  
      result.chooseActive()  
    @results.postFilters()

  getConfig: =>
    config = {}
    for key in @resultFilters
      config[key] = @[key].getConfig()
    for key in @voyageFilters
      config[key] = @[key].getConfig()
    if @rt
      for key in @rtVoyageFilters
        config[key] = @[key].getConfig()
    console.log('getConfig',config)
    return config

  setConfig: (config)=>
    console.log('setConfig',config)
    for key,cfg of config
      @[key].setConfig(cfg)


class HotelFiltersT
  constructor: (@results)->
    @template = 'hotels-filters'

    # Fixme stopover filter will cause filter method to run twice!
    # onlyDirect should actually be result filter
    @roomFilters = ['price']
    @hotelFilters = ['services','stars','distance','hotelName']


    #@stars = new ListFilter(['stars'], @results.departureCity, 'Все аэропорты')
    @services = new ListFilter(['hotelServices'], 'Дополнительно', 'Все услуги')

    @stars = new StarsFilter(['stars'], 'Дополнительно', 'Все услуги')

    @price = new PriceFilter('pricePerNight')

    @distance = new DistancesFilter('distanceToCenter')
    @hotelName = new TextFilter('hotelName','поиск по названию')

    # Saves us from multiple @filter calls on initial load
    @refilter = (ko.computed =>
      for key in @hotelFilters
        @[key].selection()
      for key in @roomFilters
        @[key].selection()
    ).extend {throttle: 50}
    @refilter.subscribe @filter


    # FIXME looks ugly
    @iterate @updateLimitsHotel, @updateLimitsRoom, false
    @services.addOption 'Фитнесс'
    @services.addOption 'Парковка'
    @services.addOption 'Интернет'
    #@results.postFilters()

  showFullMap: =>
    console.log('FM')
    console.log()
    @results.showFullMapFunc()
  updateLimitsHotel: (result) =>
    @runFiltersFunc result, @hotelFilters, 'updateLimits'

  updateLimitsRoom: (roomSet) =>
    visible = true
    @runFiltersFunc roomSet, @roomFilters, 'updateLimits'


  #FIXME knockout shoud handle it itself for scalar types need to check
  setVisibleIfChanged:(item, visible) ->
    if item.visible() == visible
      return
    #console.log('visible changed')
    #if typeof item.price != 'undefined'
    #  console.log('item with price '+item.price() + ' visible is '+visible)
    item.visible(visible)

  filterHotel: (result) =>
    @runFilters result, @hotelFilters

  filterRoom: (roomSet) =>
    @runFilters roomSet, @roomFilters

  # Runs given filter against given item
  runFilters:(item, filterSet) =>
    visible = true
    for filter_key in filterSet
      visible = visible && @[filter_key].filter(item)
      if !visible
        break
    @setVisibleIfChanged(item, visible)

  runFiltersFunc:(item, filterSet, methodName) =>
    for filter_key in filterSet
      @[filter_key][methodName](item)

  filter: =>
    @iterate @filterHotel, @filterRoom

  iterate: (onHotel, onRoom, fromInt = true) =>
    for result in @results.data()
      onHotel(result)
      if result.visible()
        someVisible = false
        for roomSet in result.roomSets()
          onRoom(roomSet)
          someVisible = someVisible || roomSet.visible()
        result.visible(someVisible)
    @results.postFilters(true)

  getConfig: =>
    config = {}
    for key in @hotelFilters
      config[key] = @[key].getConfig()
    for key in @roomFilters
      config[key] = @[key].getConfig()
    console.log('getConfig',config)
    return config

  setConfig: (config)=>
    console.log('setConfig',config)
    for key,cfg of config
      @[key].setConfig(cfg)
