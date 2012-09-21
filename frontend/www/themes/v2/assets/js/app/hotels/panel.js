var HotelsPanel, PanelRoom;

PanelRoom = (function() {

  function PanelRoom() {
    this.adults = ko.observable(1);
    this.children = ko.observable(0);
  }

  return PanelRoom;

})();

HotelsPanel = (function() {

  function HotelsPanel() {
    this.template = 'hotels-panel-template';
    this.rooms = ko.observableArray([[new PanelRoom]]);
    this.calendarText = "Выберите уже чтонибдь";
  }

  return HotelsPanel;

})();
