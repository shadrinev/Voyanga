/*
Hotels module
Controller + panel
*/

var HotelsModule;

HotelsModule = (function() {

  function HotelsModule() {
    this.panel = new HotelsPanel();
    this.controller = new HotelsController();
  }

  return HotelsModule;

})();
