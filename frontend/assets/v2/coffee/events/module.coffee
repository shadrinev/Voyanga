###
Avia module
Controller + panel
###
class EventModule
  constructor: ->
    @panel = new AviaPanel()
    @controller = new EventController @panel.sp

  resize: ->
    ResizeAvia()