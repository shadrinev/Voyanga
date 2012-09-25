###
SEARCH controller, should be splitted once we will get more actions here
###
class ToursController
  constructor: (@searchParams)->
    @routes =
      '': @searchAction

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "TOURS: Invoking searchAction", args
    # update search params with values in route
    # tempoprary development cache
    key = "tours_6"

    if sessionStorage.getItem(key) && (window.location.host != 'test.voyanga.com')
      window.voyanga_debug "TOURS: Getting result from cache"
      @handleResults(JSON.parse(sessionStorage.getItem(key)))
    else
      window.voyanga_debug "TOURS: Getting results via JSONP"
      $.ajax
        url: "http://api.voyanga.com/v1/tour/search?start=BCN&destinations%5B0%5D%5Bcity%5D=MOW&destinations%5B0%5D%5BdateFrom%5D=01.10.2012&destinations%5B0%5D%5BdateTo%5D=10.10.2012&rooms%5B0%5D%5Badt%5D=1&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0"
        dataType: 'jsonp'
        success: @handleResults

  handleResults: (data) =>
    window.voyanga_debug "searchAction: handling results", data

    # temporary development cache
    key = "tours_6"
    sessionStorage.setItem(key, JSON.stringify(data))
    stacked = new ToursResultSet data
    @trigger "results", stacked
    @render 'results', stacked

#    @trigger "sidebarChanged", filters
    ko.processAllDeferredBindingUpdates()

  render: (view, data) ->
    @trigger "viewChanged", view, data
