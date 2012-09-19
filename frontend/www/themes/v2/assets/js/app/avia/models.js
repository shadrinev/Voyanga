var AviaResult, AviaResultSet, FlightPart, SearchParams, Voyage,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

FlightPart = (function() {

  function FlightPart(part) {
    this.departureDate = new Date(part.datetimeBegin + '+04:00');
    this.arrivalDate = new Date(part.datetimeEnd + '+04:00');
    this.departureCity = part.departureCity;
    this.departureAirport = part.departureAirport;
    this.arrivalCity = part.arrivalCity;
    this.arrivalCityPre = part.arrivalCityPre;
    this.arrivalAirport = part.arrivalAirport;
    this._duration = part.duration;
    this.transportAirline = part.transportAirline;
    this.transportAirlineName = part.transportAirlineNameEn;
    this.flightCode = part.transportAirline + ' ' + part.flightCode;
    this.stopoverLength = 0;
  }

  FlightPart.prototype.departureTime = function() {
    return dateUtils.formatTime(this.departureDate);
  };

  FlightPart.prototype.arrivalTime = function() {
    return dateUtils.formatTime(this.arrivalDate);
  };

  FlightPart.prototype.duration = function() {
    return dateUtils.formatDuration(this._duration);
  };

  FlightPart.prototype.calculateStopoverLength = function(anotherPart) {
    return this.stopoverLength = Math.floor((anotherPart.departureDate.getTime() - this.arrivalDate.getTime()) / 1000);
  };

  FlightPart.prototype.stopoverText = function() {
    return dateUtils.formatDuration(this.stopoverLength);
  };

  return FlightPart;

})();

Voyage = (function() {

  function Voyage(flight) {
    var index, part, _i, _j, _len, _len1, _ref, _ref1;
    this.parts = [];
    _ref = flight.flightParts;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      part = _ref[_i];
      this.parts.push(new FlightPart(part));
    }
    this.stopoverLength = 0;
    this.direct = this.parts.length === 1;
    if (!this.direct) {
      _ref1 = this.parts;
      for (index = _j = 0, _len1 = _ref1.length; _j < _len1; index = ++_j) {
        part = _ref1[index];
        if (index < (this.parts.length - 1)) {
          part.calculateStopoverLength(this.parts[index + 1]);
        }
        this.stopoverLength += part.stopoverLength;
      }
    }
    this.departureDate = new Date(flight.departureDate + '+04:00');
    this.arrivalDate = new Date(this.parts[this.parts.length - 1].arrivalDate + '+04:00');
    this._duration = flight.fullDuration;
    this.departureAirport = this.parts[0].departureAirport;
    this.arrivalAirport = this.parts[this.parts.length - 1].arrivalAirport;
    this.departureCity = flight.departureCity;
    this.arrivalCity = flight.arrivalCity;
    this.departureCityPre = flight.departureCityPre;
    this.arrivalCityPre = flight.arrivalCityPre;
    this._backVoyages = [];
    this.activeBackVoyage = ko.observable();
    this.visible = ko.observable(true);
  }

  Voyage.prototype.departureInt = function() {
    return this.departureDate.getTime();
  };

  Voyage.prototype.hash = function() {
    return this.departureTime() + this.arrivalTime();
  };

  Voyage.prototype.push = function(voyage) {
    return this._backVoyages.push(voyage);
  };

  Voyage.prototype.stacked = function() {
    var count, result, voyage, _i, _len, _ref;
    result = false;
    count = 0;
    _ref = this._backVoyages;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      voyage = _ref[_i];
      if (voyage.visible()) {
        count++;
      }
      if (count > 1) {
        result = true;
        break;
      }
    }
    return result;
  };

  Voyage.prototype.departureDayMo = function() {
    return dateUtils.formatDayMonth(this.departureDate);
  };

  Voyage.prototype.departurePopup = function() {
    return dateUtils.formatDayMonthWeekday(this.departureDate);
  };

  Voyage.prototype.departureTime = function() {
    return dateUtils.formatTime(this.departureDate);
  };

  Voyage.prototype.departureTimeNumeric = function() {
    return dateUtils.formatTimeInMinutes(this.departureDate);
  };

  Voyage.prototype.arrivalDayMo = function() {
    return dateUtils.formatDayMonth(this.arrivalDate);
  };

  Voyage.prototype.arrivalTime = function() {
    return dateUtils.formatTime(this.arrivalDate);
  };

  Voyage.prototype.arrivalTimeNumeric = function() {
    return dateUtils.formatTimeInMinutes(this.arrivalDate);
  };

  Voyage.prototype.duration = function() {
    return dateUtils.formatDuration(this._duration);
  };

  Voyage.prototype.stopoverText = function() {
    var part, result, _i, _len, _ref;
    if (this.direct) {
      return "Без пересадок";
    }
    result = [];
    _ref = this.parts.slice(0, -1);
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      part = _ref[_i];
      result.push(part.arrivalCityPre);
    }
    return "Пересадка в " + result.join(', ');
  };

  Voyage.prototype.stopsRatio = function() {
    var data, htmlResult, index, left, part, result, _i, _j, _k, _len, _len1, _len2, _ref;
    result = [];
    if (this.direct) {
      return result;
    }
    _ref = this.parts.slice(0, -1);
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      part = _ref[_i];
      result.push(Math.ceil(part.duration / this._duration * 80));
    }
    for (index = _j = 0, _len1 = result.length; _j < _len1; index = ++_j) {
      data = result[index];
      if (index > 1) {
        result[index] = result[index - 1] + data;
      } else {
        result[index] = data + 10;
      }
    }
    htmlResult = "";
    for (_k = 0, _len2 = result.length; _k < _len2; _k++) {
      left = result[_k];
      htmlResult += '<span class="cup" style="left: ' + left + '%;"></span>';
    }
    htmlResult += '<span class="down"></span>';
    return htmlResult;
  };

  Voyage.prototype.sort = function() {
    this._backVoyages.sort(function(a, b) {
      return a.departureInt() - b.departureInt();
    });
    return this.activeBackVoyage(this._backVoyages[0]);
  };

  Voyage.prototype.chooseActive = function() {
    var active;
    if (this._backVoyages.length === 0) {
      return;
    }
    if (this.activeBackVoyage().visible()) {
      return;
    }
    active = _.find(this._backVoyages, function(voyage) {
      return voyage.visible();
    });
    if (!active) {
      this.visible(false);
      return;
    }
    return this.activeBackVoyage(active);
  };

  return Voyage;

})();

AviaResult = (function() {

  function AviaResult(data) {
    this.closeDetails = __bind(this.closeDetails, this);

    this.showDetails = __bind(this.showDetails, this);

    this.minimizeRtStacked = __bind(this.minimizeRtStacked, this);

    this.minimizeStacked = __bind(this.minimizeStacked, this);

    this.chooseNextRtStacked = __bind(this.chooseNextRtStacked, this);

    this.choosePrevRtStacked = __bind(this.choosePrevRtStacked, this);

    this.chooseRtStacked = __bind(this.chooseRtStacked, this);

    this.chooseNextStacked = __bind(this.chooseNextStacked, this);

    this.choosePrevStacked = __bind(this.choosePrevStacked, this);

    this.chooseStacked = __bind(this.chooseStacked, this);

    var fields, flights, name, rtName, _i, _len,
      _this = this;
    _.extend(this, Backbone.Events);
    flights = data.flights;
    this.price = Math.ceil(data.price);
    this._stacked = false;
    this.roundTrip = flights.length === 2;
    this.visible = ko.observable(true);
    this.airline = data.valCompany;
    this.airlineName = data.valCompanyNameEn;
    this.serviceClass = data.serviceClass;
    this.activeVoyage = new Voyage(flights[0]);
    if (this.roundTrip) {
      this.activeVoyage.push(new Voyage(flights[1]));
    }
    this.voyages = [];
    this.voyages.push(this.activeVoyage);
    this.activeVoyage = ko.observable(this.activeVoyage);
    this.stackedMinimized = ko.observable(true);
    this.rtStackedMinimized = ko.observable(true);
    fields = ['departureCity', 'departureAirport', 'departureDayMo', 'departurePopup', 'departureTime', 'arrivalCity', 'arrivalAirport', 'arrivalDayMo', 'arrivalTime', 'duration', 'direct', 'stopoverText', 'departureTimeNumeric', 'arrivalTimeNumeric', 'hash', 'stopsRatio'];
    for (_i = 0, _len = fields.length; _i < _len; _i++) {
      name = fields[_i];
      this[name] = (function(name) {
        return function() {
          var field;
          field = this.activeVoyage()[name];
          if ((typeof field) === 'function') {
            return field.apply(this.activeVoyage());
          }
          return field;
        };
      })(name);
      rtName = 'rt' + name.charAt(0).toUpperCase() + name.slice(1);
      this[rtName] = (function(name) {
        return function() {
          var field;
          field = this.activeVoyage().activeBackVoyage()[name];
          if ((typeof field) === 'function') {
            return field.apply(this.activeVoyage().activeBackVoyage());
          }
          return field;
        };
      })(name);
    }
  }

  AviaResult.prototype.stacked = function() {
    var count, voyage, _i, _len, _ref;
    count = 0;
    _ref = this.voyages;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      voyage = _ref[_i];
      if (voyage.visible()) {
        count++;
      }
      if (count > 1) {
        return true;
      }
    }
    return false;
  };

  AviaResult.prototype.rtStacked = function() {
    var count, voyage, _i, _len, _ref;
    count = 0;
    _ref = this.activeVoyage()._backVoyages;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      voyage = _ref[_i];
      if (voyage.visible()) {
        count++;
      }
      if (count > 1) {
        return true;
      }
    }
    return false;
  };

  AviaResult.prototype.push = function(data) {
    var backVoyage, newVoyage, result;
    this._stacked = true;
    newVoyage = new Voyage(data.flights[0]);
    if (this.roundTrip) {
      backVoyage = new Voyage(data.flights[1]);
      newVoyage.push(backVoyage);
      result = _.find(this.voyages, function(voyage) {
        return voyage.hash() === newVoyage.hash();
      });
      if (result) {
        result.push(backVoyage);
        return;
      }
    }
    return this.voyages.push(newVoyage);
  };

  AviaResult.prototype.chooseStacked = function(voyage) {
    var backVoyage, hash;
    window.voyanga_debug("Choosing stacked voyage", voyage);
    if (this.roundTrip) {
      hash = this.activeVoyage().activeBackVoyage().hash();
    }
    this.activeVoyage(voyage);
    backVoyage = _.find(voyage._backVoyages, function(el) {
      return el.hash() === hash;
    });
    if (backVoyage) {
      return this.activeVoyage().activeBackVoyage(backVoyage);
    }
  };

  AviaResult.prototype.choosePrevStacked = function() {
    var active_index, index, voyage, _i, _len, _ref;
    active_index = 0;
    _ref = this.voyages;
    for (index = _i = 0, _len = _ref.length; _i < _len; index = ++_i) {
      voyage = _ref[index];
      if (voyage.hash() === this.hash()) {
        active_index = index;
      }
    }
    if (active_index === 0) {
      return;
    }
    return this.activeVoyage(this.voyages[active_index - 1]);
  };

  AviaResult.prototype.chooseNextStacked = function() {
    var active_index, index, voyage, _i, _len, _ref;
    active_index = 0;
    _ref = this.voyages;
    for (index = _i = 0, _len = _ref.length; _i < _len; index = ++_i) {
      voyage = _ref[index];
      if (voyage.hash() === this.hash()) {
        active_index = index;
      }
    }
    if (active_index === this.voyages.length - 1) {
      return;
    }
    return this.activeVoyage(this.voyages[active_index + 1]);
  };

  AviaResult.prototype.chooseRtStacked = function(voyage) {
    window.voyanga_debug("Choosing RT stacked voyage", voyage);
    return this.activeVoyage().activeBackVoyage(voyage);
  };

  AviaResult.prototype.choosePrevRtStacked = function() {
    var active_index, index, rtVoyages, voyage, _i, _len;
    active_index = 0;
    rtVoyages = this.rtVoyages();
    for (index = _i = 0, _len = rtVoyages.length; _i < _len; index = ++_i) {
      voyage = rtVoyages[index];
      if (voyage.hash() === this.rtHash()) {
        active_index = index;
      }
    }
    if (active_index === 0) {
      return;
    }
    return this.activeVoyage().activeBackVoyage(rtVoyages[active_index - 1]);
  };

  AviaResult.prototype.chooseNextRtStacked = function() {
    var active_index, index, rtVoyages, voyage, _i, _len;
    active_index = 0;
    rtVoyages = this.rtVoyages();
    for (index = _i = 0, _len = rtVoyages.length; _i < _len; index = ++_i) {
      voyage = rtVoyages[index];
      if (voyage.hash() === this.rtHash()) {
        active_index = index;
      }
    }
    if (active_index === rtVoyages.length - 1) {
      return;
    }
    return this.activeVoyage().activeBackVoyage(rtVoyages[active_index + 1]);
  };

  AviaResult.prototype.minimizeStacked = function() {
    return this.stackedMinimized(!this.stackedMinimized());
  };

  AviaResult.prototype.minimizeRtStacked = function() {
    return this.rtStackedMinimized(!this.rtStackedMinimized());
  };

  AviaResult.prototype.rtVoyages = function() {
    return this.activeVoyage()._backVoyages;
  };

  AviaResult.prototype.sort = function() {
    this.voyages.sort(function(a, b) {
      return a.departureInt() - b.departureInt();
    });
    if (this.roundTrip) {
      _.each(this.voyages, function(x) {
        return x.sort();
      });
    }
    return this.activeVoyage(this.voyages[0]);
  };

  AviaResult.prototype.showDetails = function() {
    var _this = this;
    window.voyanga_debug("Setting popup result", this);
    this.trigger("popup", this);
    $('body').prepend('<div id="popupOverlay"></div>');
    $('#avia-body-popup').show();
    ko.processAllDeferredBindingUpdates();
    SizeBox('avia-popup-body');
    ResizeBox('avia-popup-body');
    return $('#popupOverlay').click(function() {
      return _this.closeDetails();
    });
  };

  AviaResult.prototype.closeDetails = function() {
    window.voyanga_debug("Hiding popup");
    $('#avia-body-popup').hide();
    return $('#popupOverlay').remove();
  };

  AviaResult.prototype.chooseActive = function() {
    var active;
    if (this.visible() === false) {
      return;
    }
    if (this.activeVoyage().visible()) {
      return;
    }
    active = _.find(this.voyages, function(voyage) {
      return voyage.visible();
    });
    if (!active) {
      this.visible(false);
      return;
    }
    return this.activeVoyage(active);
  };

  return AviaResult;

})();

AviaResultSet = (function() {

  function AviaResultSet(rawVoyages) {
    this.updateCheapest = __bind(this.updateCheapest, this);

    this.postFilters = __bind(this.postFilters, this);

    this.injectSearchParams = __bind(this.injectSearchParams, this);

    var flightVoyage, key, result, _i, _len, _ref,
      _this = this;
    this._results = {};
    for (_i = 0, _len = rawVoyages.length; _i < _len; _i++) {
      flightVoyage = rawVoyages[_i];
      key = flightVoyage.price + "_" + flightVoyage.valCompany;
      if (this._results[key]) {
        this._results[key].push(flightVoyage);
      } else {
        result = new AviaResult(flightVoyage);
        this._results[key] = result;
        result.key = key;
        result.on("popup", function(data) {
          return _this.popup(data);
        });
      }
    }
    this.cheapest = ko.observable();
    this.data = [];
    this.numResults = ko.observable(0);
    _ref = this._results;
    for (key in _ref) {
      result = _ref[key];
      result.sort();
      this.data.push(result);
    }
    this.postFilters();
    this.popup = ko.observable(this.cheapest());
  }

  AviaResultSet.prototype.injectSearchParams = function(sp) {
    this.arrivalCity = sp.destinations[0].arrival;
    this.departureCity = sp.destinations[0].departure;
    this.date = dateUtils.formatDayShortMonth(new Date(sp.destinations[0].date + '+04:00'));
    return this.roundTrip = sp.isRoundTrip;
  };

  AviaResultSet.prototype.hideRecommend = function(context, event) {
    return hideRecomendedBlockTicket.apply(event.currentTarget);
  };

  AviaResultSet.prototype.postFilters = function() {
    var data;
    data = _.filter(this.data, function(el) {
      return el.visible();
    });
    console.log("CHEAPEST", this.data.length, data.length);
    this.numResults(data.length);
    this.updateCheapest(data);
    ko.processAllDeferredBindingUpdates();
    return ResizeAvia();
  };

  AviaResultSet.prototype.updateCheapest = function(data) {
    var new_cheapest;
    if (data.length === 0) {
      return;
    }
    new_cheapest = _.reduce(data, function(el1, el2) {
      if (el1.price < el2.price) {
        return el1;
      } else {
        return el2;
      }
    }, data[0]);
    if (this.cheapest() === void 0) {
      this.cheapest(new_cheapest);
      return;
    }
    if (this.cheapest().key !== new_cheapest.key) {
      return this.cheapest(new_cheapest);
    }
  };

  return AviaResultSet;

})();

SearchParams = (function() {

  function SearchParams() {
    this.dep = ko.observable('');
    this.arr = ko.observable('');
    this.date = '06.10.2012';
    this.adults = ko.observable(1).extend({
      integerOnly: 'adult'
    });
    this.children = ko.observable(0).extend({
      integerOnly: true
    });
    this.infants = ko.observable(0).extend({
      integerOnly: 'infant'
    });
    this.rt = ko.observable(false);
    this.rtDate = '14.10.2012';
  }

  SearchParams.prototype.url = function() {
    var params, result;
    result = 'http://api.voyanga.com/v1/flight/search/BE?';
    params = [];
    params.push('destinations[0][departure]=' + this.dep());
    params.push('destinations[0][arrival]=' + this.arr());
    params.push('destinations[0][date]=' + this.date);
    if (this.rt()) {
      params.push('destinations[1][departure]=' + this.arr());
      params.push('destinations[1][arrival]=' + this.dep());
      params.push('destinations[1][date]=' + this.rtDate);
    }
    params.push('adt=' + this.adults());
    params.push('chd=' + this.children());
    params.push('inf=' + this.infants());
    result += params.join("&");
    window.voyanga_debug("Generated search url", result);
    return result;
  };

  SearchParams.prototype.key = function() {
    var key;
    key = this.dep() + this.arr() + this.date;
    if (this.rt()) {
      key += this.rtDate;
      key += '_rt';
    }
    key += this.adults();
    key += this.children();
    key += this.infants();
    console.log("Search key", key);
    return key;
  };

  SearchParams.prototype.getHash = function() {
    var hash, parts;
    parts = [this.dep(), this.arr(), this.date, this.adults(), this.children(), this.infants()];
    if (this.rt()) {
      parts.push(this.rtDate);
    }
    hash = 'avia/search/' + parts.join('/') + '/';
    window.voyanga_debug("Generated hash for avia search", hash);
    return hash;
  };

  SearchParams.prototype.fromList = function(data) {
    this.dep(data[0]);
    this.arr(data[1]);
    this.date = data[2];
    this.adults(data[3]);
    this.children(data[4]);
    this.infants(data[5]);
    if (data.length === 7) {
      this.rt(true);
      console.log("RTDATE");
      return this.rtDate = data[6];
    }
  };

  return SearchParams;

})();
