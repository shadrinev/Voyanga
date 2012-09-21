var TourEntry, ToursAviaResultSet, ToursHotelsResultSet, ToursResultSet,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

TourEntry = (function() {

  function TourEntry() {
    this.price = __bind(this.price, this);

    this.isHotel = __bind(this.isHotel, this);

    this.isAvia = __bind(this.isAvia, this);

  }

  TourEntry.prototype.isAvia = function() {
    return this.avia;
  };

  TourEntry.prototype.isHotel = function() {
    return this.hotels;
  };

  TourEntry.prototype.price = function() {
    return this.selection().price;
  };

  return TourEntry;

})();

ToursAviaResultSet = (function(_super) {

  __extends(ToursAviaResultSet, _super);

  function ToursAviaResultSet(raw, searchParams) {
    this.searchParams = searchParams;
    this.destinationText = __bind(this.destinationText, this);

    this.template = 'avia-results';
    this.panel = new AviaPanel();
    this.results = new AviaResultSet(raw);
    this.results.injectSearchParams(this.searchParams);
    this.results.postInit();
    this.data = {
      results: this.results
    };
    this.avia = true;
    this.selection = ko.observable(this.results.data[0]);
  }

  ToursAviaResultSet.prototype.destinationText = function() {
    return this.results.departureCity + ' &rarr; ' + this.results.arrivalCity;
  };

  return ToursAviaResultSet;

})(TourEntry);

ToursHotelsResultSet = (function(_super) {

  __extends(ToursHotelsResultSet, _super);

  function ToursHotelsResultSet(raw, searchParams) {
    this.searchParams = searchParams;
    this.destinationText = __bind(this.destinationText, this);

    this.template = 'hotels-results';
    this.panel = new HotelsPanel();
    this.results = new HotelsResultSet(raw);
    this.data = {
      results: this.results
    };
    this.hotels = true;
    this.selection = ko.observable(this.results.data[0].roomSets[0]);
  }

  ToursHotelsResultSet.prototype.destinationText = function() {
    return "Отель в " + this.searchParams.city;
  };

  return ToursHotelsResultSet;

})(TourEntry);

ToursResultSet = (function() {

  function ToursResultSet(raw) {
    var variant, _i, _len, _ref,
      _this = this;
    this.data = [];
    _ref = raw.allVariants;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      variant = _ref[_i];
      if (variant.flights) {
        this.data.push(new ToursAviaResultSet(variant.flights.flightVoyages, variant.searchParams));
      } else {
        this.data.push(new ToursHotelsResultSet(variant.hotels, variant.searchParams));
      }
    }
    this.selected = ko.observable(this.data[0]);
    this.panel = ko.computed(function() {
      return _this.selected().panel;
    });
    this.price = ko.computed(function() {
      var item, sum, _j, _len1, _ref1;
      sum = 0;
      _ref1 = _this.data;
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        item = _ref1[_j];
        sum += item.price();
      }
      return sum;
    });
  }

  return ToursResultSet;

})();
