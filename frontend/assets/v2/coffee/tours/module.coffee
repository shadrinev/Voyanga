###
Tours module
Controller + panel
###
class ToursModule
  constructor: ->
    @panel = ko.observable null
    @p = new TourPanelSet()
    @controller = new ToursController(@p.sp)
    @controller.on 'results', (results) =>
      @panel results.panel
      ko.processAllDeferredBindingUpdates()

    @controller.on 'index', (results) =>
      # FIXME FIMXE
      @panel @p

  resize: ->
    # FIXME
    ResizeAvia()