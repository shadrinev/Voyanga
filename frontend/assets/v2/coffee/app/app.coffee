# Base class for out application
# Handles routing, preloader screens(?), filters pane
# FIXME maybe modules is not that good idea?
class Application extends Backbone.Router
  constructor: ->
    # FIXME
    window.onerror = (error)-> new ErrorPopup('e500withText', [error]);

    # register url hash changes handler
#    hasher.initialized.add @navigate
#    hasher.changed.add @navigate

    # register 404 handler
#    crossroads.bypassed.add(@http404)

    # FIXME
    @activeModule = ko.observable null #ko.observable window.activeModule || 'avia'
    @activeModuleInstance = ko.observable null
    @activeSearchPanel = ko.observable null

    result =
      template:''
      data:{}
      rt: -> true
      departureDate: -> '12.11.2013'
      arrivalDate: -> '12.12.2013'
      calendarText:'DOH'
      minimizeCalendar: -> true
      calendarHidden: -> true
      calendarShadow: -> true
      afterRender: ->

    @fakoPanel = ko.observable result

    @panel = ko.computed =>
      am = @activeModuleInstance()
      if am
        result = ko.utils.unwrapObservable am.panel
        # We are actually depend on model observable not module`s one
        result = ko.utils.unwrapObservable result
        if result != null
          @fakoPanel result
          @activeSearchPanel(@fakoPanel())
          ko.processAllDeferredBindingUpdates()

    @_view = ko.observable false

    # Full path to view to render
    @activeView = ko.computed =>
      # If we directly navigate one of our pages 
      # we want empty template to be rendered while
      # ajax is going
      if !@_view()
        return 'stub'
      @activeModule() + '-' + @_view()

    @in1 = ko.observable 0
    @indexMode = ko.computed =>
      @in1(@_view() == 'index')

    @calendarInitialized = false

    @showEventsPicture = ko.computed =>
      @activeView() == 'tours-index'

    # View model for currently active view
    @viewData = ko.observable {}

    @slider = new Slider()
    @slider.init()
    @activeModule.subscribe @slider.handler

  initCalendar: =>
    throw "Deprecated"

    #if (!@calendarInitialized)
    #  new Calendar(@fakoPanel)
    #  @calendarInitialized = true
  minimizeCalendar: =>
    @activeSearchPanel().minimizedCalendar(true)


  reRenderCalendar:(elements) =>
    console.log('rerender calendar')
    VoyangaCalendarStandart.init @fakoPanel, elements[1]
    @fakoPanel.subscribe( (newPanel)=>
      console.log('now set new panel',newPanel)
      if newPanel.panels
        @activeSearchPanel(_.last(newPanel.panels()))
    )
    if @fakoPanel().panels
      @activeSearchPanel(_.last(@fakoPanel().panels()))

  reRenderCalendarEvent:(elements) =>
    console.log('rerender calendar')
    $('.calenderWindow').css('position','static').find('.calendarSlide').css('position','static')
    VoyangaCalendarStandart.init @itemsToBuy.activePanel, elements[1]
    @activeSearchPanel(_.last(@itemsToBuy.activePanel().panels()))

  render: (data, view)=>
#    $('#loadWrapBg').show()
    @viewData(data)
    @_view(view)
    $(window).resize()
    

  # Register routes from controller
  #
  # @param prefix url prefix for given controller
  # @param controler - controller to register
  register: (prefix, module, isDefault=false)->
    controller = module.controller

    # Change view when controller wants to
    controller.on "viewChanged", (view, data)=>
      @render(data, view)

    for route, action of controller.routes
      window.voyanga_debug "APP: registreing route", prefix, route, action
      @route prefix + route, prefix, action
      # Register appplication-wide default action
      if isDefault && route == ''
        @route route, prefix, action

    # FIXME extract to method
    # Handles module switching
    @on "beforeroute:" + prefix, (args...)->
      window.voyanga_debug "APP: routing", args
      # hide sidebar
      if @panel() == undefined || (prefix != @activeModule())
        window.voyanga_debug "APP: switching active module to", prefix
        @activeModule(prefix)
        window.voyanga_debug "APP: activating panel", ko.utils.unwrapObservable module.panel

        @activeModuleInstance module
        $(window).unbind 'resize'
        $(window).resize module.resize
        ko.processAllDeferredBindingUpdates()

  run: ->
#    @route ':path', 'h404', @handle404
#    @route ':path/*path', 'h404', @handle404
    # Start listening to hash changes
    Backbone.history.start()
    # Call some change handlers with initial values
    @bindEvents()
    @slider.handler(@activeModule())
    
  runWithModule: (module) =>
    # set default module
    Backbone.history.start({silent: true})
    window.app.navigate '#'+ module, {replace: true}
    @activeModule module
    $(window).unbind 'resize'
    $(window).resize ResizeAvia
    $(window).resize()

  bindEvents: =>
    ev = []
    $.each window.eventsRaw, (i, el) ->
      ev.push new Event(el)
    @events = new EventSet(ev)

  bindItemsToBuy: =>
    tourTrip = new TourTripResultSet(window.tripRaw)
    @itemsToBuy =  tourTrip

  bindItemsToEvent: =>
    tourTrip = new EventTourResultSet(window.tripRaw,window.eventId)
    @itemsToBuy =  tourTrip

  # FIXME write better handler
  handle404: =>
    new ErrorPopup 'avia500'

  # beforeroute event, cuz backbone cant do this for us
  route: (route, name, callback) ->
    Backbone.Router.prototype.route.call this, route, name, ->
            @trigger.apply(@, ['beforeroute:' + name].concat(_.toArray(arguments)))
            callback.apply(@, arguments)

  contentRendered: =>
    window.voyanga_debug "APP: Content rendered"
    @trigger @activeModule() + ':contentRendered'
    ResizeFun()
    WidthMine()

  mapRendered: (elem) =>
    $('.slideTours').find('.active').find('.triangle').animate({'top' : '-16px'}, 200);

  isNotEvent: =>
    !@isEvent();

  isEvent: =>
    console.log 'Checking isEvent ', @activeView()
    @activeView() == 'tours-index'

window.voyanga_debug = (args...)->
  console.log.apply(console, args)
