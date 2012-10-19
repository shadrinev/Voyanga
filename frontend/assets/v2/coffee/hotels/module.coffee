###
Hotels module
Controller + panel
###
class HotelsModule
  constructor: ->
    @panel = new HotelsPanel()
    @controller = new HotelsController(@panel.sp)

  resize: ->
    ResizeAvia()