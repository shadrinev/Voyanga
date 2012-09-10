###
SEARCH controller, should be splitted once we will get more actions here
###
class HotelsController
  constructor: ->
    @routes =
      '/search/:from/:to/:when/': @searchAction
      '': @indexAction

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "HOTELS: Invoking searchAction", args
    # update search params with values in route

    # temporary development cache
    key = "h_search_1231"
    if sessionStorage.getItem(key)
      window.voyanga_debug "HOTELS: Getting result from cache"
      @handleResults(JSON.parse(sessionStorage.getItem(key)))
    else
      window.voyanga_debug "HOTELS: Getting results via JSONP"
      $.ajax
        url: "http://api.misha.voyanga/v1/hotel/search?city=MOW&checkIn=2012-10-11&duration=3&rooms%5B0%5D%5Badt%5D=2&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0"
        dataType: 'jsonp'
        success: @handleResults

  handleResults: (data) =>
    window.voyanga_debug "HOTELS: searchAction: handling results", data

    # temporary development cache
    key = "h_search_1231"
    sessionStorage.setItem(key, JSON.stringify(data))
    stacked = new HotelsResultSet data.hotels

    @render 'results', {'results' :stacked}


  indexAction: =>
    window.voyanga_debug "HOTELS: indexAction"
    @searchAction()

  render: (view, data) ->
    @trigger "viewChanged", view, data
