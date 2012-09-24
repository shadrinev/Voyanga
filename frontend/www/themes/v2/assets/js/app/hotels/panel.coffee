class PanelRoom
  constructor: ->
    @adults = ko.observable 1
    @children = ko.observable 0

class HotelsPanel extends SearchPanel
  constructor: ->
    super()
    @template = 'hotels-panel-template'
    @rooms = ko.observableArray [[new PanelRoom ]]
    @calendarText = "Выберите уже чтонибдь"

