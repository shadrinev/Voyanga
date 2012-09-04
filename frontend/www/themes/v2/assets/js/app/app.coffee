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
  register: (prefix, controller, isDefault=false)->
    # Change view when controller wants to
    controller.on "viewChanged", (view, data)=>
      @viewData(data)
      @_view(view)

    controller.on "sidebarChanged", (sidebar, data)=>
      @sidebarData data
      @_sidebar sidebar


    controller.on "panelChanged", (panel)=>
      @panel panel

    for route, action of controller.routes
      @route prefix + route, prefix, action
      # Register appplication-wide default action
      if isDefault && route == ''
        @route route, prefix, action

  run: ->
    # Start listening to hash changes
    Backbone.history.start()

  # FIXME write better handler
  http404: ->
    alert "Not found"


$ ->
  window.VOYANGA_DEBUG = true
  # FIXME FIXME FIXME
  app = new Application()
  app.register 'avia', new AviaController(), true
  app.run()
  ko.applyBindings(app)
  window.app = app
