var SearchPanel,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

SearchPanel = (function() {

  function SearchPanel() {
    this.showCalendar = __bind(this.showCalendar, this);

    this.minimizeCalendar = __bind(this.minimizeCalendar, this);

    this.minimize = __bind(this.minimize, this);

    this.toggleCalendar = __bind(this.toggleCalendar, this);

    this.togglePanel = __bind(this.togglePanel, this);

    this.show = __bind(this.show, this);

    this.afterRender = __bind(this.afterRender, this);

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
    this.inside = false;
    this.inside2 = false;
    this.inside3 = false;
  }

  SearchPanel.prototype.afterRender = function() {
    var _this = this;
    $('.how-many-man .popup').find('input').hover(function() {
      $(this).parent().find('.plusOne').show();
      return $(this).parent().find('.minusOne').show();
    });
    $('.adults,.childs,.small-childs').hover(null, function() {
      $(this).parent().find('.plusOne').hide();
      return $(this).parent().find('.minusOne').hide();
    });
    $('.plusOne').unbind('hover');
    $('.plusOne').hover(function() {
      $(this).addClass('active');
      return $('.minusOne').addClass('active');
    }, function() {
      $(this).removeClass('active');
      return $('.minusOne').removeClass('active');
    });
    $('.minusOne').unbind('hover');
    $('.minusOne').hover(function() {
      $(this).addClass('active');
      return $('.plusOne').addClass('active');
    }, function() {
      $(this).removeClass('active');
      return $('.plusOne').removeClass('active');
    });
    $('.how-many-man').find('.popup').unbind('hover');
    $('.how-many-man').find('.popup').hover(function() {
      return _this.inside = true;
    }, function() {
      return _this.inside = false;
    });
    $('.how-many-man .content').unbind('hover');
    $('.how-many-man .content').hover(function() {
      return _this.inside2 = true;
    }, function() {
      return _this.inside2 = false;
    });
    $('.how-many-man .btn').unbind('hover');
    return $('.how-many-man .btn').hover(function() {
      return _this.inside3 = true;
    }, function() {
      return _this.inside3 = false;
    });
  };

  SearchPanel.prototype.show = function(context, event) {
    var el,
      _this = this;
    el = $(event.currentTarget);
    if (!el.hasClass('active')) {
      $(document.body).mousedown(function() {
        if (_this.inside || _this.inside2 || _this.inside3) {
          return;
        }
        return _this.close();
      });
      $('.how-many-man .btn').addClass('active');
      $('.how-many-man .content').addClass('active');
      return $('.how-many-man').find('.popup').addClass('active');
    } else {
      return this.close();
    }
  };

  SearchPanel.prototype.close = function() {
    $(document.body).unbind('mousedown');
    $('.how-many-man .btn').removeClass('active');
    $('.how-many-man .content').removeClass('active');
    return $('.how-many-man').find('.popup').removeClass('active');
  };

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
