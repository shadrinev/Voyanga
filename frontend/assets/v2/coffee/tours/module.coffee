###
Tours module
Controller + panel
###
class ToursModule
  constructor: ->
    @panel = ko.observable new TourPanelSet()
    @controller = new ToursController(@panel().sp)
    @controller.on 'results', (results) =>
      @panel results.panel

  resize: ->
    # FIXME
    ResizeAvia()