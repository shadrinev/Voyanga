/*
SEARCH controller, should be splitted once we will get more actions here
*/

var ToursController,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __slice = [].slice;

ToursController = (function() {

  function ToursController(searchParams) {
    this.searchParams = searchParams;
    this.handleResults = __bind(this.handleResults, this);

    this.searchAction = __bind(this.searchAction, this);

    this.api = new ToursAPI;
    this.routes = {
      '': this.searchAction
    };
    this.key = "tours_10";
    _.extend(this, Backbone.Events);
  }

  ToursController.prototype.searchAction = function() {
    var args;
    args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
    window.voyanga_debug("TOURS: Invoking searchAction", args);
    return this.api.search(this.handleResults);
  };

  ToursController.prototype.handleResults = function(data) {
    var stacked;
    window.voyanga_debug("searchAction: handling results", data);
    stacked = new ToursResultSet(data);
    this.trigger("results", stacked);
    this.render('results', stacked);
    return ko.processAllDeferredBindingUpdates();
  };

  ToursController.prototype.render = function(view, data) {
    return this.trigger("viewChanged", view, data);
  };

  return ToursController;

})();
