class PanelRoom
  constructor: ->
    @adults = ko.observable 1
    @children = ko.observable 0

class HotelsPanel
  constructor: ->
    @rooms = ko.observableArray [[new PanelRoom ]]


