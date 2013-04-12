# Model for avia search params,
# Used in AviaPanel and search controller
class AviaSearchParams
  constructor: ->
    @date = ko.observable ''
    if(window.currentCityCode)
      @dep = ko.observable window.currentCityCode
    else
      @dep = ko.observable 'LED'
    @arr = ko.observable ''
    @rt = ko.observable true
    @rtDate = ko.observable ''
    @passengers = new Passengers()
    @adults = @passengers.adults
    @children = @passengers.children
    @infants = @passengers.infants

  url: ->
    if (window.isLuxury == '1')
      result = 'flight/search/BEF?'
    else
      result = 'flight/search/BE?'
    params = []
    params.push 'destinations[0][departure]=' + @dep()
    params.push 'destinations[0][arrival]=' + @arr()
    params.push 'destinations[0][date]=' + moment(@date()).format('D.M.YYYY')
    if @rt()
      params.push 'destinations[1][departure]=' + @arr()
      params.push 'destinations[1][arrival]=' + @dep()
      params.push 'destinations[1][date]=' + moment(@rtDate()).format('D.M.YYYY')

    params.push 'adt=' + @adults()
    params.push 'chd=' + @children()
    params.push 'inf=' + @infants()
    result += params.join "&"
    return result

  key: ->
    key = @dep() + @arr() + @date()
    if @rt()
      key += @rtDate()
      key += '_rt'
    key += @adults()
    key += @children()
    key += @infants()
    return key

  hash: ->
    parts =  [@dep(), @arr(), moment(@date()).format('D.M.YYYY'), @adults(), @children(), @infants()]
    if @rt()
      parts.push moment(@rtDate()).format('D.M.YYYY')
    hash = 'avia/search/' + parts.join('/') + '/'
    return hash

  fromString: (data)->
    data = PEGHashParser.parse(data,'AVIA')
    @dep data.from
    @arr data.to
    @date data.dateFrom
    @adults data.passangers.adults
    @children data.passangers.children
    @infants data.passangers.infants
    @rt data.rt
    if data.rt
      @rtDate data.rtDateFrom


  fromObject: (data)=>
    @adults data.adt
    @children data.chd
    @infants data.inf
    @rt data.isRoundTrip
    @dep data.destinations[0].departure_iata
    @arr data.destinations[0].arrival_iata
    # FIXME dates are fuckd
    @date new Date(data.destinations[0].date)
    if @rt()
      @rtDate new Date(data.destinations[1].date)

  GAKey: =>
    @dep() + '/' + @arr()

  GAData: =>
    result = ''
    if @rt()
      result += '2'
    else
      result += '1'
    passangers = [@adults(), @children(), @infants()]
    result +=', ' + passangers.join(" - ")
    result += ', ' + moment(@date()).format('D.M.YYYY')
    if @rt()
      result += ' - ' + moment(@rtDate()).format('D.M.YYYY')
    result += ', ' + moment(@date()).diff(moment(), 'days')
    if @rt()
      result += ' - ' + moment(@rtDate()).diff(moment(@date()), 'days')
    return result

implement(AviaSearchParams, ISearchParams)