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
        throw new Error("Successfull api call with wrong/error response")
      @stacked = @handleResults data
      @stacked.on 'inner-template', (data)=>
        @trigger 'inner-template', data
      @trigger "results", @stacked
      @render 'results', @stacked
      ko.processAllDeferredBindingUpdates()

  handleResults: (data) =>
    console.log "Handling results", data
    #data.allVariants[0].flights.flightVoyages = []
    stacked = new ToursResultSet data, @searchParams

    if data.items
      items = []
      for item in data.items
        if(item.isHotel)
          hotel = new HotelResult item, stacked, item.duration, item, item.hotelDetails

          items.push hotel
        else
          items.push new AviaResult(item, stacked)
      if(stacked.findAndSelectItems(items))
        stacked.showOverview()
        console.log('ssseeellleecctt',items,true)
        #need save to tours
        postData = []
        for resultSet in stacked.data()
          if resultSet.isAvia()
            postData.push resultSet.selection().getPostData()
          else
            postData.push resultSet.selection().hotel.getPostData()
          console.log('result:',  resultSet.selection())
        console.log('post data',postData,@searchParams)
        $.ajax
          url: @api.endpoint + 'tour/search/updateEvent'
          data: {eventId:@searchParams.eventId,startCity:@searchParams.startCity(), items:postData}
          dataType: 'json'
          timeout: 90000
          type: 'POST'
          success: (data)=>
            #sessionStorage.setItem("#{@endpoint}#{url}", JSON.stringify(data))
            cb(data)
            if showLoad
              $('#loadWrapBg').hide()
              loaderChange(false)
          error: ->
            #
      else
        console.log('ssseeellleecctt',items,false)


    stacked.checkTicket = @checkTicketAction
    return stacked

  # FIXME reread
  checkTicketAction: (toursData, resultDeferred)=>
    now = moment()
    diff = now.diff(@stacked.creationMoment, 'seconds')
    if diff < TOURS_TICKET_TIMELIMIT
      resultDeferred.resolve(@stacked)
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
