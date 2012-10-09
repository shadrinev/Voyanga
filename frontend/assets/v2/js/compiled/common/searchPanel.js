var SearchPanel,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

SearchPanel = (function() {

  function SearchPanel(hideCalendar) {
    var _this = this;
    if (hideCalendar == null) {
      hideCalendar = true;
    }
    this.afterRender = __bind(this.afterRender, this);

    this.handlePanelSubmit = __bind(this.handlePanelSubmit, this);

    this.showCalendar = __bind(this.showCalendar, this);

    this.minimizeCalendar = __bind(this.minimizeCalendar, this);

    this.minimize = __bind(this.minimize, this);

    this.toggleCalendar = __bind(this.toggleCalendar, this);

    this.togglePanel = __bind(this.togglePanel, this);

    this.minimized = ko.observable(!hideCalendar);
    this.minimizedCalendar = ko.observable(hideCalendar);
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
      ResizeAvia();
      this.calendarHidden(false);
      $('.calenderWindow .calendarSlide').animate({
        'top': '0px'
      });
      return $('.calenderWindow').animate({
        'height': '341px'
      }, speed);
    } else {
      ResizeAvia();
      this.calendarShadow(true);
      $('.calenderWindow .calendarSlide').animate({
        'top': '-341px'
      });
      $('.calenderWindow').animate({
        'height': '0px'
      }, speed, function() {});
      this.calendarHidden(true);
      return this.calendarShadow(false);
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
    $('.calenderWindow').show();
    if (this.minimizedCalendar()) {
      return this.minimizedCalendar(false);
    }
  };

  SearchPanel.prototype.handlePanelSubmit = function() {
    app.navigate(this.sp.getHash(), {
      trigger: true
    });
    return this.minimizedCalendar(true);
  };

  SearchPanel.prototype.afterRender = function() {
    throw "Implement me";
  };

  return SearchPanel;

})();
