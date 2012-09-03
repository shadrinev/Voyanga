class AviaController
  constructor: ->
    @routes =
      '/search/{from}/{to}/{when}/': @searchAction
      '': @indexAction
    @viewChanged = new signals.Signal()
    @sidebarChanged = new signals.Signal()
    @panelChanged = new signals.Signal()
    @panel = new AviaPanel()

  searchAction: =>
    @panelChanged.dispatch @panel

    if sessionStorage.getItem("search_" + @panel.sp.key())
      @handleResults(JSON.parse(sessionStorage.getItem("search_" + @panel.sp.key())))
    else
      $.ajax
        url: @panel.sp.url()
        dataType: 'jsonp'
        success: @handleResults

  handleResults: (data) =>
    sessionStorage.setItem("search_" + @panel.sp.key(), JSON.stringify(data))
    stacked = new ResultSet data.flights.flightVoyages
    @render 'results', {'results' :stacked}
    @sidebarChanged.dispatch 'filters', {'firstNameN': [], 'lastNameN': [], 'fullNameN': [], 'results' :stacked}


  indexAction: =>
    @render 'index', {}

  render: (view, data) ->
    @panelChanged.dispatch(@panel)
    @viewChanged.dispatch(view, data)
