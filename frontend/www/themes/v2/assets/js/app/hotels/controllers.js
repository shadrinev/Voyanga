/*
SEARCH controller, should be splitted once we will get more actions here
*/

var HotelsController,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __slice = [].slice;

HotelsController = (function() {

  function HotelsController() {
    this.indexAction = __bind(this.indexAction, this);

    this.handleResults = __bind(this.handleResults, this);

    this.searchAction = __bind(this.searchAction, this);
    this.routes = {
      '/search/:from/:to/:when/': this.searchAction,
      '': this.indexAction
    };
    _.extend(this, Backbone.Events);
  }

  HotelsController.prototype.searchAction = function() {
    var args, key;
    args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
    window.voyanga_debug("HOTELS: Invoking searchAction", args);
    key = "h_search_10004";
    if (sessionStorage.getItem(key)) {
      window.voyanga_debug("HOTELS: Getting result from cache");
      return this.handleResults(JSON.parse(sessionStorage.getItem(key)));
    } else {
      window.voyanga_debug("HOTELS: Getting results via JSONP");
      return $.ajax({
        url: "http://api.voyanga.com/v1/hotel/search?city=LED&checkIn=2012-10-11&duration=3&rooms%5B0%5D%5Badt%5D=2&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0",
        dataType: 'jsonp',
        success: this.handleResults
      });
    }
  };

  HotelsController.prototype.handleResults = function(data) {
    var key, stacked;
    window.voyanga_debug("HOTELS: searchAction: handling results", data);
    key = "h_search_10004";
    sessionStorage.setItem(key, JSON.stringify(data));
    stacked = new HotelsResultSet(data.hotels);
    this.render('results', {
      'results': stacked
    });
    return this.trigger("sidebarChanged", 'filters', {
      'results': stacked
    });
  };

  HotelsController.prototype.indexAction = function() {
    window.voyanga_debug("HOTELS: indexAction");
    return this.searchAction();
  };

  HotelsController.prototype.render = function(view, data) {
    window.voyanga_debug("HOTELS: rendering", view, data);
    return this.trigger("viewChanged", view, data);
  };

  return HotelsController;

})();
