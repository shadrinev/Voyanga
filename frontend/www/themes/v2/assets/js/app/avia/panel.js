var AviaPanel, MAX_TRAVELERS,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

MAX_TRAVELERS = 9;

AviaPanel = (function() {

  function AviaPanel() {
    this.selectRoundTrip = __bind(this.selectRoundTrip, this);

    this.selectOneWay = __bind(this.selectOneWay, this);

    var _this = this;
    this.rt = ko.observable(false);
    this.minimized = ko.observable(false);
    this.adults = ko.observable(1).extend({
      integerOnly: true
    });
    this.children = ko.observable(0).extend({
      integerOnly: true
    });
    this.infants = ko.observable(0).extend({
      integerOnly: true
    });
    this.sum_children = ko.computed(function() {
      return _this.children() * 1 + _this.infants() * 1;
    });
    this.overall = ko.computed(function() {
      return _this.adults() * 1 + _this.children() * 1 + _this.infants() * 1;
    });
    this.rt.subscribe(function(newValue) {
      if (newValue) {
        return $('.tumblr .switch').animate({
          'left': '35px'
        }, 200);
      } else {
        return $('.tumblr .switch').animate({
          'left': '-1px'
        }, 200);
      }
    });
    this.minimized.subscribe(function(minimized) {
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
    });
    $(function() {
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
      return $('.how-many-man .popup').find('input').blur(function() {
        if ($(this).val() === '') {
          $(this).val($(this).attr('rel'));
        }
        $(this).trigger('change');
        if (_this.adults() === 0) {
          _this.adults(1);
        }
        if (_this.overall() > MAX_TRAVELERS) {
          _this.adults(MAX_TRAVELERS);
          _this.children(0);
          return _this.infants(0);
        }
      });
    });
  }

  AviaPanel.prototype.selectOneWay = function() {
    return this.rt(false);
  };

  AviaPanel.prototype.selectRoundTrip = function() {
    return this.rt(true);
  };

  AviaPanel.prototype.minimize = function() {
    if (this.minimized()) {
      return this.minimized(false);
    } else {
      return this.minimized(true);
    }
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

  return AviaPanel;

})();

/*
$(function() {

function initPeoplesInputs() {
  $('.how-many-man .popup').find('input').eq(0).keyup(changeAdultsCount);
  $('.how-many-man .popup').find('input').eq(1).keyup(changeChildCount);
  $('.how-many-man .popup').find('input').eq(2).keyup(changeInfantCount);



}
});
*/

