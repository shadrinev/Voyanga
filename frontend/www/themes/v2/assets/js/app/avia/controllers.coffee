class SearchParams
  constructor: ->
    @dep = 'MOW'
    @arr = 'PAR'
    @date = '02.10.2012'
    @rt = false
    @rt_date = '12.10.2012'
  
  url: ->
    result = 'http://api.misha.voyanga/v1/flight/search/withParams?'
    params = []
    params.push 'destinations[0][departure]=' + @dep
    params.push 'destinations[0][arrival]=' + @arr
    params.push 'destinations[0][date]=' + @date
    if @rt
      params.push 'destinations[1][departure]=' + @arr
      params.push 'destinations[1][arrival]=' + @dep
      params.push 'destinations[1][date]=' + @rt_date
    result += params.join "&"

  key: ->
    key = @dep + @arr + @date
    if @rt
      key += @rt_date
    return key

sp = new SearchParams

class AviaController
  constructor: ->
    @routes = 
      '/search/{from}/{to}/{when}/': @searchAction
      '': @indexAction

  searchAction: =>
    if sessionStorage.getItem("search_" + sp.key())
      @handleResults(JSON.parse(sessionStorage.getItem("search_" + sp.key())))
    else
      $.ajax
        url: sp.url()
        dataType: 'jsonp'
        success: @handleResults

  handleResults: (data) ->
    sessionStorage.setItem("search_" + sp.key(), JSON.stringify(data))
    stacked = new ResultSet data.flights.flightVoyages
    ko.applyBindings({'results': stacked}, $('#content')[0])

  indexAction : =>
    @searchAction()