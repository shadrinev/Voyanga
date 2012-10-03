/*
Hotels module
Controller + panel
*/

var HotelsModule;

HotelsModule = (function() {

  function HotelsModule() {
    this.panel = new HotelsPanel();
    this.controller = new HotelsController(this.panel.sp);
  }

  HotelsModule.prototype.resize = function() {};

  return HotelsModule;

})();
