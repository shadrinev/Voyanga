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

    

class TimeFilter extends Filter
  constructor: (@key)->
    # so we can query limits better
    @limits = ko.rangeObservable 1440, 0
    @selection = ko.rangeObservable 0,1440

    # Mix in events
    _.extend @, Backbone.Events

  filter:  (result)=>
    Utils.inRange result[@key](), @selection()


  updateLimits: (item)->
    value = @get(item, @key)
    limits = @limits()
    if value < limits.from
      limits.from = value
    if value > limits.to
      limits.to = value
    # FIXME hardcofe KOSTIL
    @limits(limits.from+';'+limits.to)

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

  updateLimits: (item)->
    for key in @keys
      value = @get(item, key)
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
    for key in @keys
      if @selection().indexOf(@get(result,key)) < 0
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
      div.hide('fast', scrollValue)
    else
      btnText = el.text el.text().replace("Все","Скрыть")
      # Update scroll pane state.
      # FIXME decouple it somehow
      div.show('fast', scrollValue)



class ShortStopoverFilter extends Filter
  constructor: ->
    @selection = ko.observable 0

  filter: (item) =>
    if @selection()
      return item.stopoverLength <= (60*60)*2.5
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
    @selection = ko.observable 0

  filter: (item) =>
    lit = @selection()
    if lit == 'A'
      return item.serviceClass == 'E'
    else
      return (item.serviceClass == 'B' || item.serviceClass == 'F')

# FIXME write comments
class AviaFiltersT
  constructor: (@results)->
    @template = 'avia-filters'
    @rt = @results.roundTrip

    @showRt = ko.observable 0
    @showRtText = ko.observable ''
    @showRt.subscribe (newValue)=>
      if +newValue
        @showRtText 'обратно'
      else
        @showRtText 'туда'

    # Fixme stopover filter will cause filter method to run twice!
    # onlyDirect should actually be result filter
    @voyageFilters = ['departure', 'arrival', 'shortStopover','onlyDirect']
    @rtVoyageFilters = ['rtDeparture', 'rtArrival', 'shortStopover','onlyDirect']
    @resultFilters = ['departureAirport', 'arrivalAirport', 'airline', 'serviceClass']
    
    @departure = new TimeFilter('departureTimeNumeric')
    @arrival = new TimeFilter('arrivalTimeNumeric')
    if @rt
      @rtDeparture = new TimeFilter('departureTimeNumeric')
      @rtArrival = new TimeFilter('arrivalTimeNumeric')


    fields = if @rt then ['departureAirport','rtArrivalAirport'] else ['departureAirport']
    @departureAirport = new ListFilter(fields, @results.departureCity, 'Все аэропорты')
    fields = if @rt then ['arrivalAirport','rtDepartureAirport'] else ['arrivalAirport']
    @arrivalAirport = new ListFilter(fields, @results.arrivalCity, 'Все аэропорты')
    @airline = new ListFilter(['airlineName'], 'Авиакомпании', 'Все авиакомпании')
    @shortStopover = new ShortStopoverFilter()
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

  