###
Tours module
Controller + panel
###
class ToursModule
  constructor: ->
    @panel = ko.observable null
    @p = new TourPanelSet()
    @innerTemplate = ''
    @controller = new ToursController(@p.sp)
    @controller.on 'results', (results) =>
      @panel results.panel
#      ko.processAllDeferredBindingUpdates()

    @controller.on 'index', (results) =>
      # FIXME FIMXE
      @panel @p

    @controller.on 'inner-template', (data) =>
      @innerTemplate = data

  resize: ->
    # FIXME
    ResizeAvia()