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

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "HOTELS: Invoking searchAction", args
    # update search params with values in route

    @searchParams.fromList(args)
    @api.search @searchParams.url(), @handleResults

  handleResults: (data) =>
    window.voyanga_debug "HOTELS: searchAction: handling results", data
    if data.error
      @render 'e500', {msg: data.error}
      return
    if !data.hotels
      @render 'e404'
      return

    # FIXME REALLY RETARDED
    data.searchParams.cacheId = data.cacheId
    stacked = new HotelsResultSet data.hotels, data.searchParams
    stacked.postInit()
    @results = ko.observable(stacked)
    @render 'results', {'results' : @results}

  indexAction: =>
    window.voyanga_debug "HOTELS: indexAction"
    @render 'index', {}

 
  timelineAction: =>
    @render 'timeline-template'
    window.setTimeout(
      ()=>
        VoyangaCalendarTimeline.calendarEvents = [
          {dayStart: Date.fromIso('2012-09-21'),dayEnd: Date.fromIso('2012-09-22'),type:'flight',color:'red',description:'Санкт-Петербург || Москва',cityFrom:'LED',cityTo:'MOW'},
          {dayStart: Date.fromIso('2012-09-21'),dayEnd: Date.fromIso('2012-09-28'),type:'hotel',color:'red',description:'Californication Hotel',city:'MOW'},
          {dayStart: Date.fromIso('2012-10-23'),dayEnd: Date.fromIso('2012-10-23'),type:'flight',color:'red',description:'Москва || Санкт-Петербург',cityFrom:'MOW',cityTo:'LED'},
          {dayStart: Date.fromIso('2012-10-23'),dayEnd: Date.fromIso('2012-10-28'),type:'hotel',color:'red',description:'Californication Hotel2',city:'LED'},
        ];
        VoyangaCalendarTimeline.init()
      ,1000
    )

  render: (view, data) ->
    window.voyanga_debug "HOTELS: rendering", view, data

    @trigger "viewChanged", view, data
