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
    @api.search @searchParams.url(), (data) =>
      if !data || data.error
        console.error 'sup'
        alert 'HANDLE ME'
      @stacked = @handleResults data
      @stacked.on 'inner-template', (data)=>
        @trigger 'inner-template', data
      @trigger "results", @stacked
      @render 'results', @stacked
      ko.processAllDeferredBindingUpdates()

  handleResults: (data) =>
    console.log "Handling results", data
    stacked = new ToursResultSet data, @searchParams
    stacked.checkTicket = @checkTicketAction
    return stacked

  checkTicketAction: (toursData, resultDeferred)=>
    now = moment()
    diff = now.diff(@stacked.creationMoment, 'seconds')
    if diff < TOURS_TICKET_TIMELIMIT
      resultDeferred.resolve(data)
      return

    @api.search  @searchParams.url(), (data)=>
      try
        stacked = @handleResults(data)
      catch err
        new ErrorPopup 'avia500'
        return
      result = stacked.findAndSelect(toursData)
      if result
        resultDeferred.resolve(stacked)
      else
        new ErrorPopup 'toursNoTicketOnValidation', false, ->
        @results stacked
    
  render: (view, data) ->
    @trigger "viewChanged", view, data
