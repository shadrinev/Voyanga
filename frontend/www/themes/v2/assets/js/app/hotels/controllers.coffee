###
SEARCH controller, should be splitted once we will get more actions here
###
class HotelsController
  constructor: ->
    @routes =
      '/search/:from/:to/:when/': @searchAction
      '/info/:cacheId/:hotelId/': @infoAction,
      '/timeline/': @timelineAction,
      '': @indexAction

    # Mix in events
    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "HOTELS: Invoking searchAction", args
    # update search params with values in route

    # temporary development cache
    key = "h_search_10100"
    window.voyanga_debug "HOTELS: Getting results via JSONP"
    $.ajax
        url: "http://api.voyanga.com/v1/hotel/search?city=LED&checkIn=2012-10-11&duration=3&rooms%5B0%5D%5Badt%5D=2&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0"
        dataType: 'jsonp'
        success: @handleResults

  handleResults: (data) =>
    window.voyanga_debug "HOTELS: searchAction: handling results", data
    stacked = new HotelsResultSet data.hotels, data.searchParams
    stacked.cacheId = data.cacheId
    @render 'results', {'results' :stacked}

  handleResultsInfo: (data) =>
    window.voyanga_debug "HOTELS: searchAction: handling results", data

    stacked = new HotelsResultSet [data.hotel], data.searchParams
    otherHotels = []
    for key,hotel of data.hotel.details
      otherHotels.push hotel
    #console.log(otherHotels)
    stackedOthers = new HotelsResultSet otherHotels, data.searchParams
    console.log stackedOthers

    @render 'info-template', {'result': stacked.data[0],'variants': stackedOthers.data[0]}


  indexAction: =>
    window.voyanga_debug "HOTELS: indexAction"
    @searchAction()

  infoAction: (args...)=>
    cacheId = args[0]
    hotelId = args[1]
    #@hotelInfoParams = new HotelInfoParams()
    #@hotelInfoParams.fromList(args)
    # temporary development cache
    key = "h_info_10002"
    window.voyanga_debug "HOTELS: Getting results via JSONP"
    $.ajax
        #url: "http://api.voyanga/v1/hotel/search?city=LED&checkIn=2012-10-11&duration=3&rooms%5B0%5D%5Badt%5D=2&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0"
        url: "http://api.voyanga.com/v1/hotel/search/info?cacheId=#{cacheId}&hotelId=#{hotelId}"
        dataType: 'jsonp'
        success: @handleResultsInfo

    #@render 'info-template'
  timelineAction: =>
    @render 'timeline-template'
    window.setTimeout(
      ()=>
        VoyangaCalendarTimeline.calendarEvents = [{dayStart: Date.fromIso('2012-09-21'),dayEnd: Date.fromIso('2012-09-22'),type:'flight',color:'red',description:'Led || Mow'},{dayStart: Date.fromIso('2012-09-21'),dayEnd: Date.fromIso('2012-09-28'),type:'hotel',color:'red',description:'Californication Hotel'},{dayStart: Date.fromIso('2012-10-23'),dayEnd: Date.fromIso('2012-10-23'),type:'flight',color:'red',description:'Mow || Led'}];
        VoyangaCalendarTimeline.init()
      ,1000
    )

  render: (view, data) ->
    window.voyanga_debug "HOTELS: rendering", view, data

    @trigger "viewChanged", view, data
