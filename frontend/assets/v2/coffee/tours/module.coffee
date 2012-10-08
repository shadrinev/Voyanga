###
Tours module
Controller + panel
###
class ToursModule
  constructor: ->
    @controller = new ToursController()# @panel.sp
    @sp = new TourSearchParams()
    @panel = ko.observable null 
    console.log 'I AM PANEL', @panel
    @controller.on 'results', (results) =>
      @panel results.panel
    @controller.on 'index', () =>
      @panel new TourPanel(@sp, 0)

  resize: ->
    # FIXME
    ResizeAvia()