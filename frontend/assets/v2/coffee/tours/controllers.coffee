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
    @trigger "index", {}
    @render 'index'
    ResizeAvia()

  searchAction: (args...)=>
    args[0] = exTrim args[0], '/'
    args = args[0].split('/')
    window.voyanga_debug "TOURS: Invoking searchAction", args
    @searchParams.fromList(args)
    @api.search @searchParams.url(), @handleResults

  handleResults: (data) =>
    console.log "Handling results", data
    if !data || data.error
      @render 'e500', {msg: data.error}
      return

    stacked = new ToursResultSet data, @searchParams
    stacked.off 'inner-template'
    stacked.on 'inner-template', (data)=>
      @trigger 'inner-template', data
      
    @trigger "results", stacked
    @render 'results', stacked


#    @trigger "sidebarChanged", filters
    ko.processAllDeferredBindingUpdates()

  render: (view, data) ->
    @trigger "viewChanged", view, data
