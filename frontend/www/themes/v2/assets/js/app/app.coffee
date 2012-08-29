# Base class for out application
# Handles routing, preloader screens(?), filters pane
class Application
  constructor: ->
    # register url hash changes handler
    hasher.initialized.add @navigate
    hasher.changed.add @navigate

    # register 404 handler
    crossroads.bypassed.add(@http404)

  # Register routes from controller
  # 
  # @param prefix url prefix for given controller
  # @param controler - controller to register
  register: (prefix, controller, isDefault=false)->
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


app = new Application()
app.register 'avia', new AviaController(), true
$ ->
  app.run()