var AviaFiltersT, Filter, ListFilter, OnlyDirectFilter, ServiceClassFilter, ShortStopoverFilter, TimeFilter,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

Filter = (function() {

  function Filter() {}

  Filter.prototype.filter = function(item) {
    throw "override me";
  };

  Filter.prototype.resetLimits = function(item) {};

  Filter.prototype.updateLimits = function(item) {};

  Filter.prototype.get = function(item, key) {
    var value;
    value = ko.utils.unwrapObservable(item[key]);
    if ((typeof value) === 'function') {
      value = value.apply(item);
    }
    return value;
  };

  return Filter;

})();

TimeFilter = (function(_super) {

  __extends(TimeFilter, _super);

  function TimeFilter(key) {
    this.key = key;
    this.filter = __bind(this.filter, this);

    this.limits = ko.rangeObservable(1440, 0);
    this.selection = ko.rangeObservable(0, 1440);
    _.extend(this, Backbone.Events);
  }

  TimeFilter.prototype.filter = function(result) {
    return true;
    return Utils.inRange(result[this.key](), this.selection());
  };

  TimeFilter.prototype.updateLimits = function(item) {
    var limits, value;
    value = this.get(item, this.key);
    limits = this.limits();
    if (value < limits.from) {
      limits.from = value;
    }
    if (value > limits.to) {
      limits.to = value;
    }
    return this.limits(limits.from + ';' + limits.to);
  };

  return TimeFilter;

})(Filter);

ListFilter = (function(_super) {

  __extends(ListFilter, _super);

  function ListFilter(keys, caption, moreLabel) {
    var _this = this;
    this.keys = keys;
    this.caption = caption;
    this.moreLabel = moreLabel;
    this.showMore = __bind(this.showMore, this);

    this.reset = __bind(this.reset, this);

    this.filter = __bind(this.filter, this);

    this.options = ko.observableArray();
    this._known = {};
    this.active = ko.computed(function() {
      return _this.options().length > 1;
    });
    this.selection = ko.computed(function() {
      var item, result, _i, _len, _ref;
      result = [];
      _ref = _this.options();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        item = _ref[_i];
        if (item.checked()) {
          result.push(item.key);
        }
      }
      return result;
    });
    _.extend(this, Backbone.Events);
  }

  ListFilter.prototype.updateLimits = function(item) {
    var key, value, _i, _len, _ref, _results;
    _ref = this.keys;
    _results = [];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      key = _ref[_i];
      value = this.get(item, key);
      if (this._known[value]) {
        continue;
      }
      this._known[value] = 1;
      _results.push(this.options.push({
        key: value,
        checked: ko.observable(0)
      }));
    }
    return _results;
  };

  ListFilter.prototype.filter = function(result) {
    var key, _i, _len, _ref;
    return true;
    if (this.selection().length === 0) {
      return true;
    }
    _ref = this.keys;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      key = _ref[_i];
      if (this.selection().indexOf(this.get(result, key)) < 0) {
        return false;
      }
    }
    return true;
  };

  ListFilter.prototype.reset = function() {
    var item, _i, _len, _ref, _results;
    _ref = this.options();
    _results = [];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      item = _ref[_i];
      _results.push(item.checked(false));
    }
    return _results;
  };

  ListFilter.prototype.showMore = function(context, event) {
    var btnText, div, el;
    el = $(event.currentTarget);
    div = el.parent().parent().find('.more-filters');
    if (!(div.css('display') === 'none')) {
      btnText = el.text(el.text().replace("Скрыть", "Все"));
      return div.hide('fast');
    } else {
      btnText = el.text(el.text().replace("Все", "Скрыть"));
      return div.show('fast');
    }
  };

  return ListFilter;

})(Filter);

ShortStopoverFilter = (function(_super) {

  __extends(ShortStopoverFilter, _super);

  function ShortStopoverFilter() {
    this.filter = __bind(this.filter, this);
    this.selection = ko.observable(0);
  }

  ShortStopoverFilter.prototype.filter = function(item) {
    if (this.selection()) {
      return item.stopoverLength <= 7200;
    }
    return true;
  };

  return ShortStopoverFilter;

})(Filter);

OnlyDirectFilter = (function(_super) {

  __extends(OnlyDirectFilter, _super);

  function OnlyDirectFilter() {
    this.filter = __bind(this.filter, this);
    this.selection = ko.observable(0);
  }

  OnlyDirectFilter.prototype.filter = function(item) {
    if (+this.selection()) {
      return item.direct;
    }
    return true;
  };

  return OnlyDirectFilter;

})(Filter);

ServiceClassFilter = (function(_super) {

  __extends(ServiceClassFilter, _super);

  function ServiceClassFilter() {
    this.filter = __bind(this.filter, this);
    this.selection = ko.observable(0);
  }

  ServiceClassFilter.prototype.filter = function(item) {
    var lit;
    console.log(item.serviceClass);
    return true;
    lit = this.selection();
    if (lit === 'A') {
      return item.serviceClass === 'E';
    } else {
      return item.serviceClass === 'B' || item.serviceClass === 'F';
    }
  };

  return ServiceClassFilter;

})(Filter);

AviaFiltersT = (function() {

  function AviaFiltersT(results) {
    var fields, key, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2,
      _this = this;
    this.results = results;
    this.iterate = __bind(this.iterate, this);

    this.filter = __bind(this.filter, this);

    this.runFiltersFunc = __bind(this.runFiltersFunc, this);

    this.runFilters = __bind(this.runFilters, this);

    this.filterBackVoyage = __bind(this.filterBackVoyage, this);

    this.filterVoyage = __bind(this.filterVoyage, this);

    this.filterResult = __bind(this.filterResult, this);

    this.updateLimitsBackVoyage = __bind(this.updateLimitsBackVoyage, this);

    this.updateLimitsVoyage = __bind(this.updateLimitsVoyage, this);

    this.updateLimitsResult = __bind(this.updateLimitsResult, this);

    this.template = 'avia-filters';
    this.rt = this.results.roundTrip;
    this.showRt = ko.observable(0);
    this.showRtText = ko.observable('');
    this.showRt.subscribe(function(newValue) {
      if (+newValue) {
        return _this.showRtText('обратно');
      } else {
        return _this.showRtText('туда');
      }
    });
    this.voyageFilters = ['departure', 'arrival', 'shortStopover', 'onlyDirect'];
    this.rtVoyageFilters = ['rtDeparture', 'rtArrival', 'shortStopover', 'onlyDirect'];
    this.resultFilters = ['departureAirport', 'arrivalAirport', 'airline', 'serviceClass'];
    this.departure = new TimeFilter('departureTimeNumeric');
    this.arrival = new TimeFilter('arrivalTimeNumeric');
    if (this.rt) {
      this.rtDeparture = new TimeFilter('departureTimeNumeric');
      this.rtArrival = new TimeFilter('arrivalTimeNumeric');
    }
    fields = this.rt ? ['departureAirport', 'rtArrivalAirport'] : ['departureAirport'];
    this.departureAirport = new ListFilter(fields, this.results.departureCity, 'Все аэропорты');
    fields = this.rt ? ['arrivalAirport', 'rtDepartureAirport'] : ['arrivalAirport'];
    this.arrivalAirport = new ListFilter(fields, this.results.arrivalCity, 'Все аэропорты');
    this.airline = new ListFilter(['airlineName'], 'Авиакомпании', 'Все авиакомпании');
    this.shortStopover = new ShortStopoverFilter();
    this.onlyDirect = new OnlyDirectFilter();
    this.serviceClass = new ServiceClassFilter();
    _ref = this.resultFilters;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      key = _ref[_i];
      this[key].selection.subscribe(this.filter);
    }
    _ref1 = this.voyageFilters;
    for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
      key = _ref1[_j];
      this[key].selection.subscribe(this.filter);
    }
    if (this.rt) {
      _ref2 = this.rtVoyageFilters;
      for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
        key = _ref2[_k];
        this[key].selection.subscribe(this.filter);
      }
    }
    this.iterate(this.updateLimitsResult, this.updateLimitsVoyage, this.updateLimitsBackVoyage);
  }

  AviaFiltersT.prototype.updateLimitsResult = function(result) {
    return this.runFiltersFunc(result, this.resultFilters, 'updateLimits');
  };

  AviaFiltersT.prototype.updateLimitsVoyage = function(voyage) {
    var visible;
    visible = true;
    return this.runFiltersFunc(voyage, this.voyageFilters, 'updateLimits');
  };

  AviaFiltersT.prototype.updateLimitsBackVoyage = function(backVoyage) {
    return this.runFiltersFunc(backVoyage, this.rtVoyageFilters, 'updateLimits');
  };

  AviaFiltersT.prototype.setVisibleIfChanged = function(item, visible) {
    if (item.visible() === visible) {
      return;
    }
    return item.visible(visible);
  };

  AviaFiltersT.prototype.filterResult = function(result) {
    return this.runFilters(result, this.resultFilters);
  };

  AviaFiltersT.prototype.filterVoyage = function(voyage) {
    return this.runFilters(voyage, this.voyageFilters);
  };

  AviaFiltersT.prototype.filterBackVoyage = function(backVoyage) {
    return this.runFilters(backVoyage, this.rtVoyageFilters);
  };

  AviaFiltersT.prototype.runFilters = function(item, filterSet) {
    var filter_key, visible, _i, _len;
    visible = true;
    for (_i = 0, _len = filterSet.length; _i < _len; _i++) {
      filter_key = filterSet[_i];
      visible = visible && this[filter_key].filter(item);
      if (!visible) {
        break;
      }
    }
    return this.setVisibleIfChanged(item, visible);
  };

  AviaFiltersT.prototype.runFiltersFunc = function(item, filterSet, methodName) {
    var filter_key, _i, _len, _results;
    _results = [];
    for (_i = 0, _len = filterSet.length; _i < _len; _i++) {
      filter_key = filterSet[_i];
      _results.push(this[filter_key][methodName](item));
    }
    return _results;
  };

  AviaFiltersT.prototype.filter = function() {
    return this.iterate(this.filterResult, this.filterVoyage, this.filterBackVoyage);
  };

  AviaFiltersT.prototype.iterate = function(onResult, onVoyage, onBackVoyage) {
    var backVoyage, result, voyage, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2;
    _ref = this.results.data;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      result = _ref[_i];
      onResult(result);
      _ref1 = result.voyages;
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        voyage = _ref1[_j];
        onVoyage(voyage);
        if (this.rt) {
          _ref2 = voyage._backVoyages;
          for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
            backVoyage = _ref2[_k];
            onBackVoyage(backVoyage);
          }
          voyage.chooseActive();
        }
      }
      result.chooseActive();
    }
    return this.results.postFilters();
  };

  return AviaFiltersT;

})();
