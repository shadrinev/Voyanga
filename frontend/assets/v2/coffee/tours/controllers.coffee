###
SEARCH controller, should be splitted once we will get more actions here
###
class ToursController
  constructor: (@searchParams)->
    @api = new ToursAPI
    @routes =
      '/search/*rest' : @searchAction
      '': @indexAction
    @key = "tours_10"

    # Mix in events
    _.extend @, Backbone.Events

  indexAction: (args...) =>
    window.voyanga_debug "TOURS: Invoking indexAction", args
    events = []
    $.each window.eventsRaw, (i, el) ->
      events.push new Event(el)
    eventSet = new EventSet(events)
    console.log "EVENT: eventset = ", eventSet
    @trigger "index"
    @render 'index', eventSet
    ResizeAvia()

  searchAction: (args...)=>
    args = args[0].split('/')
    window.voyanga_debug "TOURS: Invoking searchAction", args
    @searchParams.fromList(args)
    @api.search @searchParams.url(), @handleResults

  handleResults: (data) =>
    window.voyanga_debug "searchAction: handling results", data

    stacked = new ToursResultSet data
    @trigger "results", stacked
    @render 'results', stacked


#    @trigger "sidebarChanged", filters
    ko.processAllDeferredBindingUpdates()

  render: (view, data) ->
    @trigger "viewChanged", view, data
