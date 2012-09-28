###
SEARCH controller, should be splitted once we will get more actions here
###
class ToursController
  constructor: (@searchParams)->
    @api = new ToursAPI
    @routes =
      '': @searchAction

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "TOURS: Invoking searchAction", args
    # update search params with values in route
    # tempoprary development cache
    @key = "tours_7"

    if sessionStorage.getItem(key) && (window.location.host != 'test.voyanga.com')
      window.voyanga_debug "TOURS: Getting result from cache"
      @handleResults(JSON.parse(sessionStorage.getItem(key)))
    else
      @api.search @handleResults

  handleResults: (data) =>
    window.voyanga_debug "searchAction: handling results", data

    # temporary development cache
    sessionStorage.setItem(@key, JSON.stringify(data))
    stacked = new ToursResultSet data
    @trigger "results", stacked
    @render 'results', stacked

#    @trigger "sidebarChanged", filters
    ko.processAllDeferredBindingUpdates()

  render: (view, data) ->
    @trigger "viewChanged", view, data
