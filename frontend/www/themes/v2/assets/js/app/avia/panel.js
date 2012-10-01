// Generated by CoffeeScript 1.3.3
var AviaPanel, EXITED, MAX_CHILDREN, MAX_TRAVELERS, balanceTravelers,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

MAX_TRAVELERS = 9;

MAX_CHILDREN = 8;

EXITED = true;

/*
Balances number of travelers, using those which was not affected by most recent user change
*/


balanceTravelers = function(others, model) {
  var delta, prop, _i, _len;
  if (model.overall() > MAX_TRAVELERS && EXITED) {
    EXITED = false;
    delta = model.overall() - MAX_TRAVELERS;
    for (_i = 0, _len = others.length; _i < _len; _i++) {
      prop = others[_i];
      if (model[prop]() >= delta) {
        model[prop](model[prop]() - delta);
        break;
      } else {
        delta -= model[prop]();
        model[prop](0);
      }
    }
  }
  return EXITED = true;
};

AviaPanel = (function(_super) {

  __extends(AviaPanel, _super);

  function AviaPanel() {
    this.show = __bind(this.show, this);

    this.handlePanelSubmit = __bind(this.handlePanelSubmit, this);

    this.selectRoundTrip = __bind(this.selectRoundTrip, this);

    this.selectOneWay = __bind(this.selectOneWay, this);

    this.setDate = __bind(this.setDate, this);

    this.afterRender = __bind(this.afterRender, this);

    var _this = this;
    AviaPanel.__super__.constructor.call(this);
    this.template = 'avia-panel-template';
    window.voyanga_debug("AviaPanel created");
    this.sp = new SearchParams();
    this.departureDate = this.sp.date;
    this.departureCity = this.sp.dep;
    this.departureCityReadable = ko.observable('');
    this.departureCityReadableGen = ko.observable('');
    this.departureCityReadableAcc = ko.observable('');
    this.rt = this.sp.rt;
    this.rtDate = this.sp.rtDate;
    this.arrivalCity = this.sp.arr;
    this.arrivalCityReadable = ko.observable('');
    this.arrivalCityReadableGen = ko.observable('');
    this.arrivalCityReadableAcc = ko.observable('');
    this.inside = false;
    this.inside2 = false;
    this.inside3 = false;
    this.oldCalendarState = this.minimizedCalendar();
    this.fromChosen = ko.computed(function() {
      if (_this.departureDate().getDay) {
        return true;
      }
      return _this.departureDate().length > 0;
    });
    this.rtFromChosen = ko.computed(function() {
      if (!_this.rt()) {
        return false;
      }
      if (_this.rtDate().getDay) {
        return true;
      }
      return _this.rtDate().length > 0;
    });
    this.formFilled = ko.computed(function() {
      var result;
      result = _this.departureCity() && _this.arrivalCity() && _this.fromChosen();
      if (_this.rt()) {
        result = result && _this.rtFromChosen();
      }
      return result;
    });
    this.maximizedCalendar = ko.computed(function() {
      return _this.departureCity() && _this.arrivalCity();
    });
    this.maximizedCalendar.subscribe(function(newValue) {
      if (newValue && (!_this.fromChosen() || !_this.rtFromChosen())) {
        return _this.showCalendar();
      }
    });
    this.calendarValue = ko.computed(function() {
      return {
        twoSelect: _this.rt(),
        from: _this.departureDate(),
        to: _this.rtDate()
      };
    });
    this.adults = this.sp.adults;
    this.children = this.sp.children;
    this.infants = this.sp.infants;
    this.departureDateDay = ko.computed(function() {
      return dateUtils.formatDay(_this.departureDate());
    });
    this.departureDateMonth = ko.computed(function() {
      return dateUtils.formatMonth(_this.departureDate());
    });
    this.rtDateDay = ko.computed(function() {
      return dateUtils.formatDay(_this.rtDate());
    });
    this.rtDateMonth = ko.computed(function() {
      return dateUtils.formatMonth(_this.rtDate());
    });
    this.adults.subscribe(function(newValue) {
      if (_this.infants() > _this.adults()) {
        _this.infants(_this.adults());
      }
      if (newValue > MAX_TRAVELERS) {
        _this.adults(MAX_TRAVELERS);
      }
      return balanceTravelers(["children", 'infants'], _this);
    });
    this.children.subscribe(function(newValue) {
      if (newValue > MAX_TRAVELERS - 1) {
        _this.children(MAX_TRAVELERS - 1);
      }
      return balanceTravelers(["adults", 'infants'], _this);
    });
    this.infants.subscribe(function(newValue) {
      if (newValue > _this.adults()) {
        _this.adults(_this.infants());
      }
      return balanceTravelers(["children", 'adults'], _this);
    });
    this.sum_children = ko.computed(function() {
      return _this.children() * 1 + _this.infants() * 1;
    });
    this.overall = ko.computed(function() {
      return _this.adults() * 1 + _this.children() * 1 + _this.infants() * 1;
    });
    this.rt.subscribe(this.rtTumbler);
    this.calendarText = ko.computed(function() {
      var result;
      result = "Выберите дату перелета ";
      if ((_this.departureCityReadable().length > 0) && (_this.arrivalCityReadable().length > 0)) {
        result += _this.departureCityReadable() + ' → ' + _this.arrivalCityReadable();
      } else if ((_this.departureCityReadable().length === 0) && (_this.arrivalCityReadable().length > 0)) {
        result += ' в ' + _this.arrivalCityReadableAcc();
      } else if ((_this.departureCityReadable().length > 0) && (_this.arrivalCityReadable().length === 0)) {
        result += ' из ' + _this.departureCityReadableGen();
      }
      return result;
    });
  }

  AviaPanel.prototype.afterRender = function() {
    var _this = this;
    return $(function() {
      $('.how-many-man .popup').find('input').hover(function() {
        $(this).parent().find('.plusOne').show();
        return $(this).parent().find('.minusOne').show();
      });
      $('.adults,.childs,.small-childs').hover(null, function() {
        $(this).parent().find('.plusOne').hide();
        return $(this).parent().find('.minusOne').hide();
      });
      $('.plusOne').hover(function() {
        $(this).addClass('active');
        return $('.minusOne').addClass('active');
      }, function() {
        $(this).removeClass('active');
        return $('.minusOne').removeClass('active');
      });
      $('.minusOne').hover(function() {
        $(this).addClass('active');
        return $('.plusOne').addClass('active');
      }, function() {
        $(this).removeClass('active');
        return $('.plusOne').removeClass('active');
      });
      $('.how-many-man .popup').find('input').focus(function() {
        $(this).attr('rel', $(this).val());
        return $(this).val('');
      });
      $('.how-many-man .popup').find('input').blur(function() {
        if ($(this).val() === '') {
          $(this).val($(this).attr('rel'));
        }
        return $(this).trigger('change');
      });
      $('.how-many-man').find('.popup').hover(function() {
        return _this.inside = true;
      }, function() {
        return _this.inside = false;
      });
      $('.how-many-man .content').hover(function() {
        return _this.inside2 = true;
      }, function() {
        return _this.inside2 = false;
      });
      $('.how-many-man .btn').hover(function() {
        return _this.inside3 = true;
      }, function() {
        return _this.inside3 = false;
      });
      _this.rtTumbler(_this.rt());
      return $('.how-many-man .btn');
    });
  };

  AviaPanel.prototype.rtTumbler = function(newValue) {
    if (newValue) {
      return $('.tumblr .switch').animate({
        'left': '35px'
      }, 200);
    } else {
      return $('.tumblr .switch').animate({
        'left': '-1px'
      }, 200);
    }
  };

  AviaPanel.prototype.setDate = function(values) {
    if (values.length) {
      this.departureDate(values[0]);
      if (values.length > 1) {
        return this.rtDate(values[1]);
      }
    }
  };

  /*
    # Click handlers
  */


  AviaPanel.prototype.selectOneWay = function() {
    return this.rt(false);
  };

  AviaPanel.prototype.selectRoundTrip = function() {
    return this.rt(true);
  };

  AviaPanel.prototype.plusOne = function(model, e) {
    var prop;
    prop = $(e.target).attr("rel");
    return model[prop](model[prop]() + 1);
  };

  AviaPanel.prototype.minusOne = function(model, e) {
    var prop;
    prop = $(e.target).attr("rel");
    return model[prop](model[prop]() - 1);
  };

  AviaPanel.prototype.handlePanelSubmit = function() {
    app.navigate(this.sp.getHash(), {
      trigger: true
    });
    return this.minimizedCalendar(true);
  };

  AviaPanel.prototype.navigateToNewSearch = function() {
    this.handlePanelSubmit();
    return this.minimizedCalendar(true);
  };

  AviaPanel.prototype.show = function(context, event) {
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

  AviaPanel.prototype.close = function() {
    $(document.body).unbind('mousedown');
    $('.how-many-man .btn').removeClass('active');
    $('.how-many-man .content').removeClass('active');
    return $('.how-many-man').find('.popup').removeClass('active');
  };

  AviaPanel.prototype.returnRecommend = function(context, event) {
    $('.recomended-content').slideDown();
    $('.order-hide').fadeIn();
    return $(event.currentTarget).animate({
      top: '-19px'
    }, 500, null, function() {
      return ResizeAvia();
    });
  };

  return AviaPanel;

})(SearchPanel);

$(document).on("autocompleted", "input.departureCity", function() {
  return $('input.arrivalCity.second-path').focus();
});

$(document).on("keyup change", "input.second-path", function(e) {
  var firstValue, secondEl;
  firstValue = $(this).val();
  secondEl = $(this).siblings('input.input-path');
  if ((e.keyCode === 8) || (firstValue.length < 3)) {
    return secondEl.val('');
  }
});
