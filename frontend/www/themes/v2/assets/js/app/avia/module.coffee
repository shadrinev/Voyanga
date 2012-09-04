###
Avia module
Controller + panel
###
class AviaModule
  constructor: ->
    @panel = new AviaPanel()
    @controller = new AviaController @panel.sp
