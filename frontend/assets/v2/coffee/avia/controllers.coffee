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

    @results = do ko.observable 

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "AVIA: Invoking searchAction", args
    # update search params with values in route
    @searchParams.fromList(args)
    do @search

  search: =>
    @api.search  @searchParams.url(), (data)=>
      try
        stacked = @handleResults(data)
      catch err
        if err=='404'
          new ErrorPopup 'avia404'
          return
        new ErrorPopup 'avia500'
        return
      @results stacked

      @render 'results', {results: @results}
      ko.processAllDeferredBindingUpdates()

  handleResults: (data) =>
    window.voyanga_debug "searchAction: handling results", data
    stacked = new AviaResultSet data.flights.flightVoyages, data.siblings
    stacked.injectSearchParams data.searchParams
    stacked.postInit()
    stacked.checkTicket = @checkTicketAction
    return stacked

  indexAction: =>
    window.voyanga_debug "AVIA: invoking indexAction"
    @render "index", {}

  
  checkTicketAction: (result, resultDeferred)=>
    now = moment()
    diff = now.diff(@results().creationMoment, 'seconds')
    if diff < AVIA_TICKET_TIMELIMIT
      resultDeferred.resolve(result)
      return

    @api.search  @searchParams.url(), (data)=>
      try
        stacked = @handleResults(data)
      catch err
        new ErrorPopup 'e500', "Не удалось проверить наличие билета."
        return
      result = stacked.findAndSelect(result)
      if result
        resultDeferred.resolve(result)
      else
        new ErrorPopup 'e500', "Билет не найден, выберите другой."
        @results stacked

  render: (view, data) ->
    @trigger "viewChanged", view, data


