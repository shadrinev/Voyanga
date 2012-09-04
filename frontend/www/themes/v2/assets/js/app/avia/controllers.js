var AviaController,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

AviaController = (function() {

  function AviaController() {
    this.indexAction = __bind(this.indexAction, this);

    this.handleResults = __bind(this.handleResults, this);

    this.searchAction = __bind(this.searchAction, this);
    this.routes = {
      '/search/:from/:to/:when/:adults/:children/:infants/': this.searchAction,
      '': this.indexAction
    };
    this.panel = new AviaPanel();
    _.extend(this, Backbone.Events);
  }

  AviaController.prototype.searchAction = function() {
    this.trigger("panelChanged", this.panel);
    if (sessionStorage.getItem("search_" + this.panel.sp.key())) {
      return this.handleResults(JSON.parse(sessionStorage.getItem("search_" + this.panel.sp.key())));
    } else {
      return $.ajax({
        url: this.panel.sp.url(),
        dataType: 'jsonp',
        success: this.handleResults
      });
    }
  };

  AviaController.prototype.handleResults = function(data) {
    var stacked;
    sessionStorage.setItem("search_" + this.panel.sp.key(), JSON.stringify(data));
    stacked = new ResultSet(data.flights.flightVoyages);
    this.render('results', {
      'results': stacked
    });
    return this.trigger("sidebarChanged", 'filters', {
      'firstNameN': [],
      'lastNameN': [],
      'fullNameN': [],
      'results': stacked
    });
  };

  AviaController.prototype.indexAction = function() {
    return this.render('index', {});
  };

  AviaController.prototype.render = function(view, data) {
    this.trigger("panelChanged", this.panel);
    return this.trigger("viewChanged", view, data);
  };

  return AviaController;

})();
