# Base class for out application
# Handles routing, preloader screens(?), filters pane
# FIXME maybe modules is not that good idea?
class Application extends Backbone.Router
  constructor: ->
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
          $('div.innerCalendar').find('h1').removeClass('highlight')

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
    @helpLayer = new HelpLayer()
    @slider.init()
    @activeModule.subscribe @slider.handler
    @debugMode = ko.observable false

    @breakdown = ko.observable false

  forceNavigate: (url) =>
    if (Backbone.history.fragment == url)
      # need to null out Backbone.history.fragement because
      # navigate method will ignore when it is the same as newFragment
      Backbone.history.fragment = null
      Backbone.history.navigate url, true
    else
      Backbone.history.navigate url, {trigger: true}

  initCalendar: =>
    throw "Deprecated"

    #if (!@calendarInitialized)
    #  new Calendar(@fakoPanel)
    #  @calendarInitialized = true
  minimizeCalendar: =>
    @activeSearchPanel().minimizedCalendar(true) if @activeSearchPanel()

  reRenderCalendar:(elements) =>
    VoyangaCalendarStandart.panel = false
    VoyangaCalendarStandart.init @fakoPanel, elements[1]
    @fakoPanel.subscribe( (newPanel)=>
      if newPanel.panels
        @activeSearchPanel(_.last(newPanel.panels()))
      else
        @activeSearchPanel(newPanel)
    )

    if @fakoPanel().panels
      @activeSearchPanel(_.last(@fakoPanel().panels()))
    else
      @activeSearchPanel(@fakoPanel())

  reRenderCalendarStatic:(elements) =>
    $('.calenderWindow').css('position','static').find('.calendarSlide').css('position','static')
    VoyangaCalendarStandart.init @fakoPanel, elements[1]
    @fakoPanel.subscribe( (newPanel)=>
      console.log('change panel',newPanel,newPanel.panels)
      if newPanel.panels
        @activeSearchPanel(_.last(newPanel.panels()))
      else
        @activeSearchPanel(newPanel)
    )
    if @fakoPanel().panels
      @activeSearchPanel(_.last(@fakoPanel().panels()))
    else
      @activeSearchPanel(@fakoPanel())

  reRenderCalendarEvent:(elements) =>
    $('.calenderWindow').css('position','static').find('.calendarSlide').css('position','static')
    VoyangaCalendarStandart.init @itemsToBuy.activePanel, elements[1]
    @activeSearchPanel(_.last(@itemsToBuy.activePanel().panels()))

  render: (data, view)=>
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
      @route prefix + route, prefix, action
      # Register appplication-wide default action
      if isDefault && route == ''
        @route route, prefix, action

    # FIXME extract to method
    # Handles module switching
    @on "beforeroute:" + prefix, (args...)->
      # hide sidebar
      if @panel() == undefined || (prefix != @activeModule())
        @minimizeCalendar()
        @activeModule(prefix)

        @activeModuleInstance module
        $(window).unbind 'resize'
        $(window).resize module.resize
        ko.processAllDeferredBindingUpdates()
        @toggleGMaps(false)

  toggleGMaps: (force) ->
    if ((@activeModule() == 'avia') || (@activeModule() == 'hotels'))
      @events.closeEventsPhoto() if (force || (@events && @events.isRendered))
    else
      @events.closeEventsMaps() if (force || (@events && @events.isRendered))

  run: ->
    Backbone.history.start()
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

  toMainPage: =>
    window.location.href = '/'

  toAviaPage: =>
    window.location.href = '/#avia'

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

  bindItemsToTour: =>
    tourTrip = new TourResultSet(window.tripRaw, window.orderId)
    @itemsToBuy =  tourTrip

  # FIXME write better handler
  handle404: =>
    new ErrorPopup 'avia500'

  # beforeroute event, cuz backbone cant do this for us
  route: (route, name, callback) ->
    Backbone.Router.prototype.route.call this, route, name, ->
      # index
      if window.location.hash.replace('#', '') != ''
        GAPush ['_trackPageview', '/' + window.location.hash.replace('#', '')]
      @trigger.apply(@, ['beforeroute:' + name].concat(_.toArray(arguments)))
      callback.apply(@, arguments)

  contentRendered: =>
    @trigger @activeModule() + ':contentRendered'
    ResizeFun()
    WidthMine()
    if !@runned
      @runned = true
      return
      
    window.VisualLoaderInstance.hide()
    window.setTimeout(
      =>
        @helpLayer.tryShow()
      , 200
    )

  mapRendered: (elem) =>
    $('.slideTours').find('.active').find('.triangle').animate({'top' : '-16px'}, 200);

  isNotEvent: =>
    !@isEvent();

  isEvent: =>
    @activeView() == 'tours-index'

window.voyanga_debug = (args...)->
  #return
  # crashes in bad browsers sometimes
  console.log.apply(console, args)
