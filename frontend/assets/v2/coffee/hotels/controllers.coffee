###
SEARCH controller, should be splitted once we will get more actions here
###
class HotelsController
  constructor: (@searchParams) ->
    @api = new HotelsAPI
    @routes =
      '/search/:from/:in/:out/*rest': @searchAction
#      '/info/:cacheId/:hotelId/': @infoAction,
      '/timeline/': @timelineAction,
      '': @indexAction

    @results = ko.observable()

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "HOTELS: Invoking searchAction", args
    # update search params with values in route

    @searchParams.fromList(args)
    @api.search @searchParams.url(), (data)=>
      try
        stacked = @handleResults data
      catch err
        if err=='e404'
          new ErrorPopup 'e404'
          return
        alert err
        new ErrorPopup 'e500'#, {msg: data.error}
        return
        
      @results stacked
      @render 'results', {'results' : @results}

  handleResults: (data) =>
    window.voyanga_debug "HOTELS: searchAction: handling results", data
    # FIXME REALLY RETARDED
    stacked = new HotelsResultSet data, data.searchParams
    stacked.postInit()
    stacked.checkTicket = @checkTicketAction
    return stacked
    
  checkTicketAction: (roomSet, resultDeferred)=>
    now = moment()
    diff = now.diff(@results().creationMoment, 'seconds')
    if diff < HOTEL_TICKET_TIMELIMIT
      resultDeferred.resolve(roomSet)
      return

    @api.search  @searchParams.url(), (data)=>
      try
        stacked = @handleResults(data)
      catch err
        new ErrorPopup 'e500', "Не удалось проверить наличие билета."
        return
      result = stacked.findAndSelect(roomSet)
      if result
        resultDeferred.resolve(result)
      else
        new ErrorPopup 'e500', "Билет не найден, выберите другой.", ->
        @results stacked


  indexAction: =>
    window.voyanga_debug "HOTELS: indexAction"
    @render 'index', {}

 
  timelineAction: =>
    @render 'timeline-template'
    window.setTimeout(
      ()=>
        VoyangaCalendarTimeline.calendarEvents = [
          {dayStart: Date.fromIso('2012-10-23'),dayEnd: Date.fromIso('2012-10-23'),type:'flight',color:'red',description:'Москва || Санкт-Петербург',cityFrom:'MOW',cityTo:'LED'},
          {dayStart: Date.fromIso('2012-10-23'),dayEnd: Date.fromIso('2012-10-28'),type:'hotel',color:'red',description:'Californication Hotel2',city:'LED'},
          {dayStart: Date.fromIso('2012-10-28'),dayEnd: Date.fromIso('2012-10-28'),type:'flight',color:'red',description:'Санкт-Петербург || Москва',cityFrom:'LED',cityTo:'MOW'},
          {dayStart: Date.fromIso('2012-10-28'),dayEnd: Date.fromIso('2012-10-28'),type:'flight',color:'red',description:'Москва || Санкт-Петербург',cityFrom:'MOW',cityTo:'LED'},
          {dayStart: Date.fromIso('2012-11-21'),dayEnd: Date.fromIso('2012-11-22'),type:'flight',color:'red',description:'Санкт-Петербург || Москва',cityFrom:'LED',cityTo:'MOW'},
          {dayStart: Date.fromIso('2012-11-21'),dayEnd: Date.fromIso('2012-11-28'),type:'hotel',color:'red',description:'Californication Hotel',city:'MOW'},
          {dayStart: Date.fromIso('2012-11-28'),dayEnd: Date.fromIso('2012-11-28'),type:'flight',color:'red',description:'Москва || Санкт-Петербург',cityFrom:'MOW',cityTo:'LED'},
          {dayStart: Date.fromIso('2012-11-28'),dayEnd: Date.fromIso('2012-11-28'),type:'flight',color:'red',description:'Санкт-Петербург || Амстердам',cityFrom:'LED',cityTo:'AMS'},
          {dayStart: Date.fromIso('2012-11-28'),dayEnd: Date.fromIso('2012-11-28'),type:'flight',color:'red',description:'Амстердам || Санкт-Петербург',cityFrom:'AMS',cityTo:'LED'},
          {dayStart: Date.fromIso('2012-11-28'),dayEnd: Date.fromIso('2012-11-28'),type:'flight',color:'red',description:'Санкт-Петербург || Москва',cityFrom:'LED',cityTo:'MOW'},
        ];
        VoyangaCalendarTimeline.calendarEvents = [
          {dayStart: Date.fromIso('2012-11-29'),dayEnd: Date.fromIso('2012-11-29'),type:'flight',color:'red',description:'Москва || Санкт-Петербург',cityFrom:'MOW',cityTo:'LED'},
          {dayStart: Date.fromIso('2012-11-29'),dayEnd: Date.fromIso('2012-12-01'),type:'hotel',color:'red',description:'Californication Hotel2',city:'LED'},
        ];
        VoyangaCalendarTimeline.init()
      ,1000
    )

  render: (view, data) ->
    window.voyanga_debug "HOTELS: rendering", view, data

    @trigger "viewChanged", view, data
