# Base class for out application
# Handles routing, preloader screens(?), filters pane
# FIXME maybe modules is not that good idea?
class Application extends Backbone.Router
  constructor: ->
    # register url hash changes handler
#    hasher.initialized.add @navigate
#    hasher.changed.add @navigate

    # register 404 handler
#    crossroads.bypassed.add(@http404)

    # FIXME
    @activeModule = ko.observable null #ko.observable window.activeModule || 'avia'
    @activeModuleInstance = ko.observable null

    @panel = ko.computed =>
      result = false
      am = @activeModuleInstance()
      if am
        result = ko.utils.unwrapObservable am.panel
      if result
        return result
      else
        return {'template':'',data:{}, calendarText:'DOH'}

    # View currently being active in given module
    @_view = ko.observable 'index'

    # Full path to view to render
    @activeView = ko.computed =>
      @activeModule() + '-' + @_view()

    # View model for currently active view
    @viewData = ko.observable {}

    @slider = new Slider()
    @slider.init()
    @activeModule.subscribe @slider.handler

  # Register routes from controller
  #
  # @param prefix url prefix for given controller
  # @param controler - controller to register
  register: (prefix, module, isDefault=false)->
    controller = module.controller
    # Change view when controller wants to
    controller.on "viewChanged", (view, data)=>
      @viewData(data)
      @_view(view)

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
    # Start listening to hash changes
    Backbone.history.start()
    # Call some change handlers with initial values
    @slider.handler(@activeModule())

  # FIXME write better handler
  http404: ->
    alert "Not found"

  # beforeroute event, cuz backbone cant do this for us
  route: (route, name, callback) ->
    Backbone.Router.prototype.route.call this, route, name, ->
            @trigger.apply(@, ['beforeroute:' + name].concat(_.toArray(arguments)))
            callback.apply(@, arguments)

  contentRendered: =>
    window.voyanga_debug "APP: Content rendered"
    @trigger @activeModule() + ':contentRendered'
    ResizeFun()

$ ->
  console.time "App dispatching"
  window.voyanga_debug = (args...) ->
    # Chrome does not likes window context for console.log, so we pass itself here
    console.log.apply console, args
  # FIXME FIXME FIXME
  app = new Application()
  avia = new AviaModule()
  hotels = new HotelsModule()
  window.app = app
  app.register 'tours', new ToursModule()
  app.register 'hotels', hotels
  app.register 'avia', avia, true
  app.run()
  console.timeEnd "App dispatching"
  console.time "Rendering"
  console.profile "Rendering"
  ko.applyBindings(app)
  console.profileEnd()
  console.timeEnd "Rendering"
