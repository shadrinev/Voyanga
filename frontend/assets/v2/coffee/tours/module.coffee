###
Tours module
Controller + panel
###
class ToursModule
  constructor: ->
    @controller = new ToursController()# @panel.sp
    @sp = new TourSearchParams()
    @panel = new TourPanel(@sp, 0)
    console.log 'I AM PANEL', @panel
    @controller.on 'results', (results) =>
      @panel results.panel

  resize: ->
    # FIXME
    ResizeAvia()