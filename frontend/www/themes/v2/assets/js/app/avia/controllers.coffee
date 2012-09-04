class AviaController
  constructor: ->
    @routes =
      '/search/:from/:to/:when/:adults/:children/:infants/': @searchAction
      '': @indexAction
    @panel = new AviaPanel()

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: =>
    @trigger "panelChanged", @panel

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
    @trigger "sidebarChanged", 'filters', {'firstNameN': [], 'lastNameN': [], 'fullNameN': [], 'results' :stacked}


  indexAction: =>
    @render 'index', {}

  render: (view, data) ->
    @trigger "panelChanged", @panel
    @trigger "viewChanged", view, data
