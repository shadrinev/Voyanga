var TourEntry, ToursAviaResultSet, ToursHotelsResultSet, ToursOverviewVM, ToursResultSet,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

TourEntry = (function() {

  function TourEntry() {
    this.rt = __bind(this.rt, this);

    this.savings = __bind(this.savings, this);

    this.maxPriceHtml = __bind(this.maxPriceHtml, this);

    this.minPriceHtml = __bind(this.minPriceHtml, this);

    this.priceHtml = __bind(this.priceHtml, this);

    this.price = __bind(this.price, this);

    this.isHotel = __bind(this.isHotel, this);

    this.isAvia = __bind(this.isAvia, this);
    _.extend(this, Backbone.Events);
  }

  TourEntry.prototype.isAvia = function() {
    return this.avia;
  };

  TourEntry.prototype.isHotel = function() {
    return this.hotels;
  };

  TourEntry.prototype.price = function() {
    if (this.selection() === null) {
      return 0;
    }
    return this.selection().price;
  };

  TourEntry.prototype.priceHtml = function() {
    if (this.selection() === null) {
      return "Не выбрано";
    }
    return this.price() + '<span class="rur">o</span>';
  };

  TourEntry.prototype.minPriceHtml = function() {
    return this.minPrice() + '<span class="rur">o</span>';
  };

  TourEntry.prototype.maxPriceHtml = function() {
    return this.maxPrice() + '<span class="rur">o</span>';
  };

  TourEntry.prototype.savings = function() {
    if (this.selection() === null) {
      return 0;
    }
    return 555;
  };

  TourEntry.prototype.rt = function() {
    return false;
  };

  return TourEntry;

})();

ToursAviaResultSet = (function(_super) {

  __extends(ToursAviaResultSet, _super);

  function ToursAviaResultSet(raw, sp) {
    this.rt = __bind(this.rt, this);

    this.dateHtml = __bind(this.dateHtml, this);

    this.dateClass = __bind(this.dateClass, this);

    this.additionalText = __bind(this.additionalText, this);

    this.destinationText = __bind(this.destinationText, this);

    this.maxPrice = __bind(this.maxPrice, this);

    this.minPrice = __bind(this.minPrice, this);

    this.numAirlines = __bind(this.numAirlines, this);

    this.overviewText = __bind(this.overviewText, this);

    this.doNewSearch = __bind(this.doNewSearch, this);

    this.newResults = __bind(this.newResults, this);
    this.api = new AviaAPI;
    this.template = 'avia-results';
    this.overviewTemplate = 'tours-overview-avia-ticket';
    this.panel = new AviaPanel();
    this.panel.handlePanelSubmit = this.doNewSearch;
    this.panel.sp.fromObject(sp);
    this.results = ko.observable();
    this.selection = ko.observable(null);
    this.newResults(raw, sp);
    this.data = {
      results: this.results
    };
  }

  ToursAviaResultSet.prototype.newResults = function(raw, sp) {
    var r, result,
      _this = this;
    result = new AviaResultSet(raw);
    result.injectSearchParams(sp);
    result.postInit();
    result.recommendTemplate = 'avia-tours-recommend';
    result.tours = true;
    result.select = function(res) {
      if (res.ribbon) {
        res = res.data;
      }
      result.selected_key(res.key);
      return _this.selection(res);
    };
    this.avia = true;
    r = result.data[0];
    result.selected_key(r.key);
    this.selection(result.data[0]);
    return this.results(result);
  };

  ToursAviaResultSet.prototype.doNewSearch = function() {
    var _this = this;
    return this.api.search(this.panel.sp.url(), function(data) {
      return _this.newResults(data.flights.flightVoyages, data.searchParams);
    });
  };

  ToursAviaResultSet.prototype.overviewText = function() {
    return "Перелет " + this.results().departureCity + ' &rarr; ' + this.results().arrivalCity;
  };

  ToursAviaResultSet.prototype.numAirlines = function() {
    return this.results().filters.airline.options().length;
  };

  ToursAviaResultSet.prototype.minPrice = function() {
    var cheapest;
    cheapest = _.reduce(this.results().data, function(el1, el2) {
      if (el1.price < el2.price) {
        return el1;
      } else {
        return el2;
      }
    }, this.results().data[0]);
    return cheapest.price;
  };

  ToursAviaResultSet.prototype.maxPrice = function() {
    var mostExpensive;
    mostExpensive = _.reduce(this.results().data, function(el1, el2) {
      if (el1.price > el2.price) {
        return el1;
      } else {
        return el2;
      }
    }, this.results().data[0]);
    return mostExpensive.price;
  };

  ToursAviaResultSet.prototype.destinationText = function() {
    return this.results().departureCity + ' &rarr; ' + this.results().arrivalCity;
  };

  ToursAviaResultSet.prototype.additionalText = function() {
    if (this.selection() === null) {
      return "";
    }
    if (this.rt()) {
      return "";
    } else {
      return ", " + this.selection().departureTime() + ' - ' + this.selection().arrivalTime();
    }
  };

  ToursAviaResultSet.prototype.dateClass = function() {
    if (this.rt()) {
      return 'blue-two';
    } else {
      return 'blue-one';
    }
  };

  ToursAviaResultSet.prototype.dateHtml = function() {
    var result, source;
    source = this.selection();
    if (source === null) {
      source = this.results().data[0];
    }
    result = '<div class="day">';
    result += dateUtils.formatHtmlDayShortMonth(source.departureDate());
    result += '</div>';
    if (this.rt()) {
      result += '<div class="day">';
      result += dateUtils.formatHtmlDayShortMonth(source.rtDepartureDate());
      result += '</div>';
    }
    return result;
  };

  ToursAviaResultSet.prototype.rt = function() {
    return this.results().roundTrip;
  };

  return ToursAviaResultSet;

})(TourEntry);

ToursHotelsResultSet = (function(_super) {

  __extends(ToursHotelsResultSet, _super);

  function ToursHotelsResultSet(raw, searchParams) {
    var hotel, room,
      _this = this;
    this.searchParams = searchParams;
    this.dateHtml = __bind(this.dateHtml, this);

    this.dateClass = __bind(this.dateClass, this);

    this.additionalText = __bind(this.additionalText, this);

    this.price = __bind(this.price, this);

    this.destinationText = __bind(this.destinationText, this);

    this.maxPrice = __bind(this.maxPrice, this);

    this.minPrice = __bind(this.minPrice, this);

    this.numHotels = __bind(this.numHotels, this);

    this.overviewText = __bind(this.overviewText, this);

    ToursHotelsResultSet.__super__.constructor.apply(this, arguments);
    this.overviewTemplate = 'tours-overview-hotels-ticket';
    this.activeHotel = ko.observable(0);
    this.template = 'hotels-results';
    this.results = new HotelsResultSet(raw, this.searchParams);
    this.results.tours(true);
    this.results.select = function(hotel) {
      hotel.off('back');
      hotel.on('back', function() {
        return _this.trigger('setActive', _this);
      });
      hotel.off('select');
      hotel.on('select', function(roomData) {
        _this.activeHotel(hotel.hotelId);
        return _this.selection(roomData);
      });
      return _this.trigger('setActive', {
        'data': hotel,
        template: 'hotels-info-template'
      });
    };
    this.data = {
      results: ko.observable(this.results)
    };
    this.hotels = true;
    this.selection = ko.observable(null);
    hotel = this.results.data[0];
    room = hotel.roomSets[0];
    this.activeHotel(hotel.hotelId);
    this.selection({
      'roomSet': room,
      'hotel': hotel
    });
  }

  ToursHotelsResultSet.prototype.overviewText = function() {
    return this.destinationText();
  };

  ToursHotelsResultSet.prototype.numHotels = function() {
    return this.results.data.length;
  };

  ToursHotelsResultSet.prototype.minPrice = function() {
    return this.results.minPrice;
  };

  ToursHotelsResultSet.prototype.maxPrice = function() {
    return this.results.maxPrice;
  };

  ToursHotelsResultSet.prototype.destinationText = function() {
    return "Отель в " + this.searchParams.city;
  };

  ToursHotelsResultSet.prototype.price = function() {
    if (this.selection() === null) {
      return 0;
    }
    return this.selection().roomSet.price();
  };

  ToursHotelsResultSet.prototype.additionalText = function() {
    if (this.selection() === null) {
      return "";
    }
    return ", " + this.selection().hotel.hotelName;
  };

  ToursHotelsResultSet.prototype.dateClass = function() {
    return 'orange-two';
  };

  ToursHotelsResultSet.prototype.dateHtml = function() {
    var result;
    result = '<div class="day">';
    result += dateUtils.formatHtmlDayShortMonth(this.results.checkIn);
    result += '</div>';
    result += '<div class="day">';
    result += dateUtils.formatHtmlDayShortMonth(this.results.checkOut);
    return result += '</div>';
  };

  return ToursHotelsResultSet;

})(TourEntry);

ToursResultSet = (function() {

  function ToursResultSet(raw) {
    this.removeItem = __bind(this.removeItem, this);

    this.showOverview = __bind(this.showOverview, this);

    this.setActive = __bind(this.setActive, this);

    var result, variant, _i, _len, _ref,
      _this = this;
    this.data = ko.observableArray();
    _ref = raw.allVariants;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      variant = _ref[_i];
      if (variant.flights) {
        this.data.push(new ToursAviaResultSet(variant.flights.flightVoyages, variant.searchParams));
      } else {
        result = new ToursHotelsResultSet(variant.hotels, variant.searchParams);
        this.data.push(result);
        result.on('setActive', function(entry) {
          return _this.setActive(entry);
        });
      }
    }
    this.selection = ko.observable(this.data()[0]);
    this.panel = ko.computed({
      read: function() {
        if (_this.selection().panel) {
          _this.panelContainer = _this.selection().panel;
        }
        return _this.panelContainer;
      }
    });
    this.price = ko.computed(function() {
      var item, sum, _j, _len1, _ref1;
      sum = 0;
      _ref1 = _this.data();
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        item = _ref1[_j];
        sum += item.price();
      }
      return sum;
    });
    this.savings = ko.computed(function() {
      var item, sum, _j, _len1, _ref1;
      sum = 0;
      _ref1 = _this.data();
      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
        item = _ref1[_j];
        sum += item.savings();
      }
      return sum;
    });
    this.vm = new ToursOverviewVM(this);
    this.showOverview();
  }

  ToursResultSet.prototype.setActive = function(entry) {
    this.selection(entry);
    ko.processAllDeferredBindingUpdates();
    return ResizeAvia();
  };

  ToursResultSet.prototype.showOverview = function() {
    return this.setActive({
      template: 'tours-overview',
      data: this
    });
  };

  ToursResultSet.prototype.removeItem = function(item, event) {
    var idx;
    event.stopPropagation();
    if (this.data().length < 2) {
      return;
    }
    idx = this.data.indexOf(item);
    console.log(this.data.indexOf(item), item, this.selection());
    if (idx === -1) {
      return;
    }
    this.data.splice(idx, 1);
    if (item === this.selection()) {
      return this.setActive(this.data()[0]);
    }
  };

  return ToursResultSet;

})();

ToursOverviewVM = (function() {

  function ToursOverviewVM(resultSet) {
    this.resultSet = resultSet;
    this.dateHtml = __bind(this.dateHtml, this);

    this.dateClass = __bind(this.dateClass, this);

    this.startCity = __bind(this.startCity, this);

  }

  ToursOverviewVM.prototype.startCity = function() {
    var firstResult;
    firstResult = this.resultSet.data()[0];
    if (firstResult.isAvia()) {
      return firstResult.results().departureCity;
    } else {
      return 'Бобруйск';
    }
  };

  ToursOverviewVM.prototype.dateClass = function() {
    return 'blue-one';
  };

  ToursOverviewVM.prototype.dateHtml = function() {
    var firstResult, result, source;
    firstResult = this.resultSet.data()[0];
    source = firstResult.selection();
    result = '<div class="day">';
    result += dateUtils.formatHtmlDayShortMonth(source.departureDate());
    result += '</div>';
    return result;
  };

  return ToursOverviewVM;

})();
