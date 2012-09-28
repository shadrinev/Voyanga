###
SEARCH controller, should be splitted once we will get more actions here
###
class AviaController
  constructor: (@searchParams)->
    @api = new AviaAPI
    @routes =
      '/search/:from/:to/:when/:adults/:children/:infants/:rtwhen/': @searchAction
      '/search/:from/:to/:when/:adults/:children/:infants/': @searchAction
      '': @indexAction

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "AVIA: Invoking searchAction", args
    # update search params with values in route
    @searchParams.fromList(args)

    # tempoprary development cache
    @api.search  @searchParams.url(), @handleResults

  handleResults: (data) =>
    
    window.voyanga_debug "searchAction: handling results", data

    # temporary development cache
    stacked = new AviaResultSet data.flights.flightVoyages
    stacked.injectSearchParams data.searchParams
    stacked.postInit()
    # we need observable here to be compatible with tours
    @render 'results', {results: ko.observable(stacked)}

    ko.processAllDeferredBindingUpdates()

  indexAction: =>
    window.voyanga_debug "AVIA: invoking indexAction"

    @render 'index', {}

  render: (view, data) ->
    @trigger "viewChanged", view, data
