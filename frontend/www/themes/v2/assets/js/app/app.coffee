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
    @activeModule = ko.observable window.activeModule || 'avia'

    @panel = ko.observable {}

    # View currently being active in given module
    @_view = ko.observable 'index'
    @_sidebar = ko.observable 'dummy'

    # Full path to view to render
    @activeView = ko.computed =>
      @activeModule() + '-' + @_view()

    @activeSidebar = ko.computed =>
      @activeModule() + '-' + @_sidebar()


    # View model for currently active view
    @viewData = ko.observable {}

    # View model for sidebar
    @sidebarData = ko.observable {}

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

    controller.on "sidebarChanged", (sidebar, data)=>
      @sidebarData data
      @_sidebar sidebar


    for route, action of controller.routes
      window.voyanga_debug "APP: registreing route", prefix, route, action
      @route prefix + route, prefix, action
      # Register appplication-wide default action
      if isDefault && route == ''
        @route route, prefix, action

    if isDefault
     @panel module.panel

    # FIXME extract to method
    # Handles module switching
    @on "route:" + prefix, (args...)->
      if prefix != @activeModule()
        window.voyanga_debug "APP: switching active module to", prefix
        @activeModule(prefix)

  run: ->
    # Start listening to hash changes
    Backbone.history.start()

  # FIXME write better handler
  http404: ->
    alert "Not found"


$ ->
  window.voyanga_debug = (args...) ->
    # Chrome does not likes window context for console, so we pass itself here
    console.log.apply console, args
  # FIXME FIXME FIXME
  app = new Application()
  avia = new AviaModule()
  app.register 'avia', avia, true
  app.run()
  ko.applyBindings(app)
  window.app = app
