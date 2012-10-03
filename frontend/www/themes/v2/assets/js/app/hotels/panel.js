var HotelsPanel,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

HotelsPanel = (function(_super) {

  __extends(HotelsPanel, _super);

  function HotelsPanel() {
    this.setDate = __bind(this.setDate, this);

    this.handlePanelSubmit = __bind(this.handlePanelSubmit, this);

    var _this = this;
    HotelsPanel.__super__.constructor.apply(this, arguments);
    this.template = 'hotels-panel-template';
    this.sp = new HotelsSearchParams();
    this.city = this.sp.city;
    this.checkIn = this.sp.checkIn;
    this.checkOut = this.sp.checkOut;
    this.rooms = this.sp.rooms;
    this.roomsView = ko.computed(function() {
      var current, item, result, _i, _len, _ref;
      result = [];
      current = [];
      _ref = _this.rooms();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        item = _ref[_i];
        if (current.length === 2) {
          result.push(current);
          current = [];
        }
        current.push(item);
      }
      result.push(current);
      return result;
    });
    this.addRoom = this.sp.addRoom;
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
