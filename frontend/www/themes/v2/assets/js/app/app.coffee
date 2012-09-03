# Base class for out application
# Handles routing, preloader screens(?), filters pane
# FIXME maybe modules is not that good idea?
class Application
  constructor: ->
    # register url hash changes handler
    hasher.initialized.add @navigate
    hasher.changed.add @navigate

    # register 404 handler
    crossroads.bypassed.add(@http404)

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
    controller.viewChanged.add (view, data)=>
      @viewData(data)
      @_view(view)

    controller.sidebarChanged.add (sidebar, data)=>
      @sidebarData data
      @_sidebar sidebar


    controller.panelChanged.add (panel)=>
      @panel panel

    for route, action of controller.routes
      crossroads.addRoute(prefix + route).matched.add(action)
      # Register appplication-wide default action
      if isDefault && route == ''
        crossroads.addRoute(route).matched.add(action)

  run: ->
    # Start listening to hash changes
    hasher.init()

  # Changes controller/template when we are going to other page/tab
  navigate: (newUrl, oldUrl)=>
    # dispatch request
    crossroads.parse(newUrl)

  # FIXME write better handler
  http404: ->
    alert "Not found"


$ ->
  app = new Application()
  app.register 'avia', new AviaController(), true
  app.run()
  ko.applyBindings(app)
