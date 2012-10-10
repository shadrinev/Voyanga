/*
Tours module
Controller + panel
*/

var ToursModule;

ToursModule = (function() {

  function ToursModule() {
    var _this = this;
    this.panel = ko.observable(null);
    this.p = new TourPanelSet();
    this.controller = new ToursController(this.p.sp);
    this.controller.on('results', function(results) {
      _this.panel(results.panel);
      return ko.processAllDeferredBindingUpdates();
    });
    this.controller.on('index', function(results) {
      return _this.panel(_this.p);
    });
  }

  ToursModule.prototype.resize = function() {
    return ResizeAvia();
  };

  return ToursModule;

})();
