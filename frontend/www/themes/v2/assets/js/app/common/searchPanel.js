var SearchPanel,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

SearchPanel = (function() {

  function SearchPanel() {
    this.showCalendar = __bind(this.showCalendar, this);

    this.minimizeCalendar = __bind(this.minimizeCalendar, this);

    this.minimize = __bind(this.minimize, this);

    this.toggleCalendar = __bind(this.toggleCalendar, this);

    this.togglePanel = __bind(this.togglePanel, this);

    var _this = this;
    this.minimized = ko.observable(false);
    this.minimizedCalendar = ko.observable(true);
    this.calendarHidden = ko.observable(this.minimizedCalendar);
    this.calendarShadow = ko.observable(this.minimizedCalendar);
    this.oldCalendarState = this.minimizedCalendar();
    this.togglePanel(this.minimized());
    this.toggleCalendar(this.minimizedCalendar());
    this.minimized.subscribe(function(minimized) {
      return _this.togglePanel(minimized);
    });
    this.minimizedCalendar.subscribe(function(minimizedCalendar) {
      return _this.toggleCalendar(minimizedCalendar);
    });
  }

  SearchPanel.prototype.togglePanel = function(minimized) {
    var heightSubHead, speed;
    speed = 300;
    heightSubHead = $('.sub-head').height();
    if (!minimized) {
      return $('.sub-head').animate({
        'margin-top': '0px'
      }, speed);
    } else {
      return $('.sub-head').animate({
        'margin-top': '-' + (heightSubHead - 4) + 'px'
      }, speed);
    }
  };

  SearchPanel.prototype.toggleCalendar = function(minimizedCalendar) {
    var heightCalendar1, heightCalendar2, heightSubHead, speed,
      _this = this;
    speed = 500;
    heightSubHead = $('.sub-head').height();
    heightCalendar1 = $('.calenderWindow').height();
    heightCalendar2 = heightSubHead;
    if (!minimizedCalendar) {
      this.calendarHidden(false);
      return $('.calenderWindow').animate({
        'top': (heightSubHead + 1) + 'px'
      }, speed);
    } else {
      this.calendarShadow(true);
      return $('.calenderWindow').animate({
        'top': '-' + heightCalendar1 + 'px'
      }, speed, function() {
        return _this.calendarShadow(false);
      });
    }
  };

  SearchPanel.prototype.minimize = function() {
    if (this.minimized()) {
      this.minimized(false);
      return this.minimizedCalendar(this.oldCalendarState);
    } else {
      this.minimized(true);
      this.oldCalendarState = this.minimizedCalendar();
      if (!this.minimizedCalendar()) {
        return this.minimizedCalendar(true);
      }
    }
  };

  SearchPanel.prototype.minimizeCalendar = function() {
    if (this.minimizedCalendar()) {
      return this.minimizedCalendar(false);
    } else {
      return this.minimizedCalendar(true);
    }
  };

  SearchPanel.prototype.showCalendar = function() {
    if (this.minimizedCalendar()) {
      return this.minimizedCalendar(false);
    }
  };

  return SearchPanel;

})();
