/*
SEARCH controller, should be splitted once we will get more actions here
*/

var AviaController,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __slice = [].slice;

AviaController = (function() {

  function AviaController(searchParams) {
    this.searchParams = searchParams;
    this.indexAction = __bind(this.indexAction, this);

    this.handleResults = __bind(this.handleResults, this);

    this.getFilterLimitValues = __bind(this.getFilterLimitValues, this);

    this.searchAction = __bind(this.searchAction, this);

    this.routes = {
      '/search/:from/:to/:when/:adults/:children/:infants/': this.searchAction,
      '': this.indexAction
    };
    _.extend(this, Backbone.Events);
  }

  AviaController.prototype.searchAction = function() {
    var args, key;
    args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
    window.voyanga_debug("AVIA: Invoking searchAction", args);
    this.searchParams.fromList(args);
    key = "search_" + this.searchParams.key();
    window.REQ_STATED = new Date().getTime();
    if (sessionStorage.getItem(key) && (window.location.host !== 'test.voyanga.com')) {
      window.voyanga_debug("AVIA: Getting result from cache");
      return this.handleResults(JSON.parse(sessionStorage.getItem(key)));
    } else {
      window.voyanga_debug("AVIA: Getting results via JSONP");
      return $.ajax({
        url: this.searchParams.url(),
        dataType: 'jsonp',
        success: this.handleResults
      });
    }
  };

  AviaController.prototype.getFilterLimitValues = function(results) {
    return console.log(results);
  };

  AviaController.prototype.handleResults = function(data) {
    var key, msg, stacked,
      _this = this;
    window.voyanga_debug("searchAction: handling results", data);
    msg = "request legth = " + ((new Date().getTime() - window.REQ_STATED) / 1000);
    alert(msg);
    key = "search_" + this.searchParams.key();
    sessionStorage.setItem(key, JSON.stringify(data));
    stacked = new AviaResultSet(data.flights.flightVoyages);
    this.getFilterLimitValues(stacked);
    this.aviaFiltersInit = {
      flightClassFilter: {
        value: data.searchParams.serviceClass
      },
      departureTimeSliderDirect: {
        fromTime: stacked.timeLimits.departureFromTime,
        toTime: stacked.timeLimits.departureToTime
      },
      arrivalTimeSliderDirect: {
        fromTime: stacked.timeLimits.arrivalFromTime,
        toTime: stacked.timeLimits.arrivalToTime
      },
      departureTimeSliderReturn: {
        fromTime: stacked.timeLimits.departureFromTimeReturn,
        toTime: stacked.timeLimits.departureToTimeReturn
      },
      arrivalTimeSliderReturn: {
        fromTime: stacked.timeLimits.arrivalFromTimeReturn,
        toTime: stacked.timeLimits.arrivalToTimeReturn
      },
      rt: data.searchParams.isRoundTrip
    };
    this.render('results', {
      'results': stacked
    });
    window.setTimeout(function() {
      return AviaFilters.init(_this.aviaFiltersInit);
    }, 1000);
    return this.trigger("sidebarChanged", 'filters', {
      'results': stacked
    });
  };

  AviaController.prototype.indexAction = function() {
    window.voyanga_debug("AVIA: invoking indexAction");
    return this.render('index', {});
  };

  AviaController.prototype.render = function(view, data) {
    return this.trigger("viewChanged", view, data);
  };

  return AviaController;

})();
