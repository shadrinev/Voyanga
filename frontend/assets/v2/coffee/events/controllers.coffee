###
SEARCH controller, should be splitted once we will get more actions here
###
class EventController
  constructor: (@searchParams)->
    @api = ''
    @routes =
      '/event/:id/': @searchAction
      '': @indexAction

    _.extend @, Backbone.Events

  searchAction: (args...)=>
    window.voyanga_debug "EVENT: Invoking searchAction", args

  handleResults: (data) =>
    window.voyanga_debug "EVENT searchAction: handling results", data

  indexAction: =>
    window.voyanga_debug "EVENT: invoking indexAction"
    events = []
    $.each window.eventsRaw, (i, el) ->
      events.push new Event(el)
    eventSet = new EventSet(events)
    console.log "EVENT: eventset = ", eventSet
    @render 'index', eventSet
    CenterIMGResize()

  render: (view, data) ->
    console.log data
    @trigger "viewChanged", view, data
