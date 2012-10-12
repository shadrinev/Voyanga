var Timeline,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

Timeline = (function() {

  function Timeline(toursData) {
    var _this = this;
    this.toursData = toursData;
    this.scrollTimelineLeft = __bind(this.scrollTimelineLeft, this);

    this.scrollTimelineRight = __bind(this.scrollTimelineRight, this);

    this.showTimeline = __bind(this.showTimeline, this);

    this.showConditions = __bind(this.showConditions, this);

    this.timelinePosition = ko.observable(0);
    this.termsActive = false;
    this.data = ko.computed(function() {
      var avia_map, end_date, hotel_map, item, item_avia, item_hotel, left, middle_date, obj, results, right, spans, start_date, timeline_length, x, _i, _j, _k, _len, _ref;
      spans = [];
      avia_map = {};
      hotel_map = {};
      _ref = _this.toursData();
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        item = _ref[_i];
        obj = {
          start: moment(item.timelineStart()),
          end: moment(item.timelineEnd())
        };
        spans.push(obj);
        if (item.isHotel()) {
          hotel_map[obj.start.format('M.D')] = {
            duration: obj.end.diff(obj.start, 'days'),
            item: item
          };
        }
        ({
          "else": avia_map[obj.start.format('M.D')] = {
            duration: obj.end.diff(obj.start, 'days'),
            item: item
          }
        });
      }
      start_date = spans[0].start;
      end_date = spans[spans.length - 1].end;
      if (true) {
        item = _this.toursData()[0];
        if (item.isAvia()) {
          if (item.rt()) {
            end_date = moment(item.rtTimelineStart());
            avia_map[end_date.format('M.D')] = {
              duration: 1
            };
          }
        }
      }
      timeline_length = end_date.diff(start_date, 'days');
      middle_date = start_date.clone().add('days', timeline_length / 2);
      if (timeline_length < 23) {
        timeline_length = 23;
      }
      left = timeline_length / 2;
      right = timeline_length / 2;
      if (timeline_length % 2) {
        right += 1;
      }
      results = [];
      for (x = _j = 1; 1 <= left ? _j <= left : _j >= left; x = 1 <= left ? ++_j : --_j) {
        obj = {
          date: middle_date.clone().subtract('days', left - x)
        };
        obj.day = obj.date.format('D');
        obj.hotel = false;
        obj.avia = false;
        item_avia = avia_map[obj.date.format('M.D')];
        item_hotel = hotel_map[obj.date.format('M.D')];
        if (item_hotel) {
          obj.hotel = item_hotel;
          obj.hotel_item = item_hotel.item;
        }
        if (item_avia) {
          obj.avia = item_avia;
          obj.avia_item = item_avia.item;
        }
        results.push(obj);
      }
      for (x = _k = 0; 0 <= right ? _k <= right : _k >= right; x = 0 <= right ? ++_k : --_k) {
        obj = {
          date: middle_date.clone().add('days', x)
        };
        obj.day = obj.date.format('D');
        obj.hotel = false;
        obj.avia = false;
        item_avia = avia_map[obj.date.format('M.D')];
        item_hotel = hotel_map[obj.date.format('M.D')];
        if (item_hotel) {
          obj.hotel = item_hotel;
        }
        if (item_avia) {
          obj.avia = item_avia;
        }
        results.push(obj);
      }
      return results;
    });
  }

  Timeline.prototype.showConditions = function(context, event) {
    var el,
      _this = this;
    el = $(event.currentTarget);
    if (!el.hasClass('active')) {
      $('.btn-timeline-and-condition a').removeClass('active');
      el.addClass('active');
      $('.timeline').addClass('hide');
      $('.timeline').animate({
        'top': '-' + $('.timeline').height() + 'px'
      }, 400, function() {
        $('.slide-tmblr').css('overflow', 'visible');
        return _this.termsActive = true;
      });
      return $('.condition').animate({
        'top': '-15px'
      }, 400).removeClass('hide');
    }
  };

  Timeline.prototype.showTimeline = function(context, event) {
    var el,
      _this = this;
    el = $(event.currentTarget);
    if (!el.hasClass('active')) {
      $('.slide-tmblr').css('overflow', 'hidden');
      $('.btn-timeline-and-condition a').removeClass('active');
      el.addClass('active');
      $('.timeline').animate({
        'top': '0px'
      }, 400).removeClass('hide');
      return $('.condition').animate({
        'top': '68px'
      }, 400, function() {
        return _this.termsActive = false;
      }).addClass('hide');
    }
  };

  Timeline.prototype.scrollTimelineRight = function() {
    var scrollableFrame;
    scrollableFrame = this.data().length * 32 - 23 * 32;
    if (scrollableFrame < 0) {
      return;
    }
    this.timelinePosition(this.timelinePosition() + 32);
    if (this.timelinePosition() > scrollableFrame) {
      return this.timelinePosition(scrollableFrame);
    }
  };

  Timeline.prototype.scrollTimelineLeft = function() {
    var scrollableFrame;
    scrollableFrame = this.data().length * 32 - 23 * 32;
    if (scrollableFrame < 0) {
      return;
    }
    this.timelinePosition(this.timelinePosition() - 32);
    if (this.timelinePosition() < 0) {
      return this.timelinePosition(0);
    }
  };

  return Timeline;

})();
