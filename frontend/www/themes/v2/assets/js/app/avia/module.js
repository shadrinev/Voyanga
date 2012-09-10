/*
Avia module
Controller + panel
*/

var AviaModule;

AviaModule = (function() {

  function AviaModule() {
    this.panel = new AviaPanel();
    this.controller = new AviaController(this.panel.sp);
  }

  return AviaModule;

})();
