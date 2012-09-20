###
Tours module
Controller + panel
###
class ToursModule
  constructor: ->
    @controller = new ToursController()# @panel.sp
    # FIXME FIXME FIXME
    @controller.on 'results', (results) =>
      @panel = results.panel

  resize: ->
    # FIXME
    ResizeAvia()