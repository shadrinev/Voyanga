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
    @trigger "index", {}
    @render 'index'
    ResizeAvia()

  searchAction: (args)=>
    window.app.helpLayer.pageName('tours')
    @searchParams.fromString args
    # Searcheng
    sp = @searchParams
    GAPush ['_trackEvent','Trip_press_button_search', sp.GAKey(), sp.GAData()]

    if @searchParams.urlChanged()
      window.VisualLoaderInstance.start(@api.loaderDescription)
      @doSearch()

      if @searchParams.hotelId()
        backUrl = window.location.hash
        urls = backUrl.split('hotelId')
        @searchParams.hotelId(false)
        window.setTimeout(
          =>
            window.app.navigate (urls[0])
          ,200
        )
    else
      if @searchParams.hotelChanged()
        if @searchParams.hotelId()
          #вырезать из урла /hotelId/123/
          backUrl = window.location.hash
          urls = backUrl.split('hotelId')
          @searchParams.hotelId(false)

          window.setTimeout(
            =>
              window.app.navigate (urls[0])
            ,200
          )
          #window.app.activeModuleInstance().controller.searchParams.hotelId(hotel.hotelId)
        else
          @searchParams.lastHotel.back()
      else
        window.VisualLoaderInstance.start(@api.loaderDescription)
        @doSearch()

  doSearch: =>
    @api.search @searchParams.url(), (data) =>
      if !data || data.error
        window.VisualLoaderInstance.start(@api.loaderDescription)
        throw new Error("Successfull api call with wrong/error response")
      @stacked = @handleResults data
      @stacked.on 'inner-template', (data)=>
        @trigger 'inner-template', data

      GAPush ['_trackEvent', 'Trip_show_search_results', @searchParams.GAKey(),  @searchParams.GAData()]
      @trigger "results", @stacked
      @render 'results', @stacked
      ko.processAllDeferredBindingUpdates()



  handleResults: (data) =>
    #data.allVariants[1].hotels = []
    stacked = new ToursResultSet data, @searchParams

    if data.items
      items = []
      for item in data.items
        if(item.isHotel)
          hotel = new HotelResult item, stacked, item.duration, item, item.hotelDetails
          hotel.searchParams = item.searchParams
          items.push hotel
        else
          avia = new AviaResult(item, stacked)
          avia.searchParams = item.searchParams
          items.push avia
      if(stacked.findAndSelectItems(items))
        stacked.showOverview()
        #need save to tours
        postData = []
        for resultSet in stacked.data()
          if resultSet.isAvia()
            postData.push resultSet.selection().getPostData()
          else
            postData.push resultSet.selection().hotel.getPostData()

        $.ajax
          url: @api.endpoint + 'tour/search/updateEvent'
          data: {eventId:@searchParams.eventId,startCity:@searchParams.startCity(), items:postData}
          dataType: 'json'
          timeout: 90000
          type: 'POST'
          success: (data)=>
            #sessionStorage.setItem("#{@endpoint}#{url}", JSON.stringify(data))
            cb(data)
    setAct = false
    if @searchParams.flightHash
      for resultSet in stacked.data()
        if resultSet.isAvia()
          res = resultSet.findAndSelectHash(@searchParams.flightHash)
          if res
            setAct = true
            continue
        if setAct
          stacked.setActive(resultSet)




    stacked.checkTicket = @checkTicketAction
    return stacked

  # FIXME reread
  checkTicketAction: (toursData, resultDeferred)=>
    now = moment()
    diff = now.diff(@stacked.creationMoment, 'seconds')
    if diff < TOURS_TICKET_TIMELIMIT
      resultDeferred.resolve(@stacked)
      return

    window.VisualLoaderInstance.start("Идет проверка выбранных выриантов<br>Это может занять от 5 до 30 секунд")
    @api.search  @searchParams.url(), (data)=>
      try
        stacked = @handleResults(data)
      catch err
        window.VisualLoaderInstance.hide()
        new ErrorPopup 'avia500'
        return
      result = stacked.findAndSelect(toursData)
      if result
        resultDeferred.resolve(stacked)
      else
        window.VisualLoaderInstance.hide()
        new ErrorPopup 'toursNoTicketOnValidation', false, ->
        @results stacked



    
  render: (view, data) ->
    @trigger "viewChanged", view, data
