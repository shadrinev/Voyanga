###
SEARCH controller, should be splitted once we will get more actions here
###
class ToursController
  constructor: (@searchParams)->
    @api = new ToursAPI
    @routes =
      '': @searchAction
    @key = "tours_10"

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "TOURS: Invoking searchAction", args
    # update search params with values in route
    # tempoprary development cache
    
    @api.search @handleResults

  handleResults: (data) =>
    window.voyanga_debug "searchAction: handling results", data

    stacked = new ToursResultSet data
    @trigger "results", stacked
    @render 'results', stacked

#    @trigger "sidebarChanged", filters
    ko.processAllDeferredBindingUpdates()

  render: (view, data) ->
    @trigger "viewChanged", view, data
