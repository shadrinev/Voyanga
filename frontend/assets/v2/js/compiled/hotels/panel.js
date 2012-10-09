var HotelsPanel,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

HotelsPanel = (function(_super) {

  __extends(HotelsPanel, _super);

  function HotelsPanel() {
    this.setDate = __bind(this.setDate, this);

    this.haveDates = __bind(this.haveDates, this);

    this.checkOutHtml = __bind(this.checkOutHtml, this);

    this.checkInHtml = __bind(this.checkInHtml, this);

    this.handlePanelSubmit = __bind(this.handlePanelSubmit, this);

    var _this = this;
    this.template = 'hotels-panel-template';
    HotelsPanel.__super__.constructor.call(this);
    this.sp = new HotelsSearchParams();
    this.calendarHidden = ko.observable(true);
    this.city = this.sp.city;
    this.checkIn = this.sp.checkIn;
    this.checkOut = this.sp.checkOut;
    this.peopleSelectorVM = new HotelPeopleSelector(this.sp);
    this.cityReadable = ko.observable();
    this.cityReadableAcc = ko.observable();
    this.cityReadableGen = ko.observable();
    this.calendarText = ko.computed(function() {
      return "vibAR->" + _this.cityReadable();
    });
    this.formFilled = ko.computed(function() {
      var cin, cout, result;
      if (_this.checkIn().getDay) {
        cin = true;
      } else {
        cin = _this.checkIn().length > 0;
      }
      if (_this.checkOut().getDay) {
        cout = true;
      } else {
        cout = _this.checkOut().length > 0;
      }
      result = _this.city() && cin && cout;
      return result;
    });
    this.maximizedCalendar = ko.computed(function() {
      return _this.city().length > 0;
    });
    this.maximizedCalendar.subscribe(function(newValue) {
      if (!newValue) {
        return;
      }
      if (_this.formFilled()) {
        return;
      }
      return _this.showCalendar();
    });
    this.calendarValue = ko.computed(function() {
      return {
        twoSelect: true,
        hotels: true,
        from: _this.checkIn(),
        to: _this.checkOut()
      };
    });
  }

  HotelsPanel.prototype.handlePanelSubmit = function() {
    app.navigate(this.sp.getHash(), {
      trigger: true
    });
    return this.minimizedCalendar(true);
  };

  HotelsPanel.prototype.checkInHtml = function() {
    if (this.checkIn()) {
      return dateUtils.formatHtmlDayShortMonth(this.checkIn());
    }
    return '';
  };

  HotelsPanel.prototype.checkOutHtml = function() {
    if (this.checkOut()) {
      return dateUtils.formatHtmlDayShortMonth(this.checkOut());
    }
    return '';
  };

  HotelsPanel.prototype.haveDates = function() {
    return this.checkOut() && this.checkIn();
  };

  HotelsPanel.prototype.navigateToNewSearch = function() {
    this.handlePanelSubmit();
    return this.minimizedCalendar(true);
  };

  HotelsPanel.prototype.setDate = function(values) {
    if (values.length) {
      this.checkIn(values[0]);
      if (values.length > 1) {
        return this.checkOut(values[1]);
      }
    }
  };

  return HotelsPanel;

})(SearchPanel);
