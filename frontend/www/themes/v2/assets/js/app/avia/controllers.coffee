###
SEARCH controller, should be splitted once we will get more actions here
###
class AviaController
  constructor: (@searchParams)->
    @routes =
      '/search/:from/:to/:when/:adults/:children/:infants/': @searchAction
      '': @indexAction

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "AVIA: Invoking searchAction", args
    # update search params with values in route
    @searchParams.fromList(args)

    # temporary development cache
    key = "search_" + @searchParams.key()
    if sessionStorage.getItem(key)
      window.voyanga_debug "AVIA: Getting result from cache"
      @handleResults(JSON.parse(sessionStorage.getItem(key)))
    else
      window.voyanga_debug "AVIA: Getting results via JSONP"
      $.ajax
        url: @searchParams.url()
        dataType: 'jsonp'
        success: @handleResults

  handleResults: (data) =>
    window.voyanga_debug "searchAction: handling results", data

    # temporary development cache
    key = "search_" + @searchParams.key()
    sessionStorage.setItem(key, JSON.stringify(data))
    stacked = new ResultSet data.flights.flightVoyages
    @render 'results', {'results' :stacked}
    @trigger "sidebarChanged", 'filters', {'firstNameN': [], 'lastNameN': [], 'fullNameN': [], 'results' :stacked}

  indexAction: =>
    @render 'index', {}

  render: (view, data) ->
    @trigger "viewChanged", view, data
