###
SEARCH controller, should be splitted once we will get more actions here
###
class EventController
  constructor: (@searchParams)->
    @api = ''
    @routes =
      '/event/:from/:id/': @searchAction
      '': @indexAction

    _.extend @, Backbone.Events

  searchAction: (args...)=>

    window.voyanga_debug "EVENT: Invoking searchAction", args
  ###    # update search params with values in route
      @searchParams.fromList(args)

      # tempoprary development cache
      @api.search  @searchParams.url(), @handleResults
  ###

  handleResults: (data) =>
    window.voyanga_debug "EVENT searchAction: handling results", data
  ###
      # temporary development cache
      stacked = new AviaResultSet data.flights.flightVoyages
      stacked.injectSearchParams data.searchParams
      stacked.postInit()
      # we need observable here to be compatible with tours
      @render 'results', {results: ko.observable(stacked)}

      ko.processAllDeferredBindingUpdates()
  ###

  indexAction: =>
    window.voyanga_debug "EVENT: invoking indexAction"
    events = []
    $.each window.eventsRaw, (i, el) ->
      events.push new Event(el)
    eventSet = new EventSet(events)
    console.log "EVENT: eventset = ", eventSet
    @render 'index', eventSet
    ResizeAvia()

  render: (view, data) ->
    console.log data
    @trigger "viewChanged", view, data
