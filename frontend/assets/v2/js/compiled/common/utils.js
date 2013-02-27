// Generated by CoffeeScript 1.4.0
var ACC_MONTHS, AVIA_TICKET_TIMELIMIT, HOTEL_TICKET_TIMELIMIT, MONTHS, SHORT_MONTHS, SHORT_WEEKDAYS, TOURS_TICKET_TIMELIMIT, Utils, calcOffset, dateUtils, exTrim, isEmail, waitElement,
  _this = this;

AVIA_TICKET_TIMELIMIT = 15 * 60;

HOTEL_TICKET_TIMELIMIT = 15 * 60;

TOURS_TICKET_TIMELIMIT = 15 * 60;

MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентрября', 'октября', 'ноября', 'декабря'];

ACC_MONTHS = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентрябрь', 'октябрь', 'ноябрь', 'декабрь'];

SHORT_MONTHS = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

SHORT_WEEKDAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

dateUtils = {
  formatDay: function(date) {
    var day, re;
    if (date.length === 0) {
      return;
    }
    if (date.getDay) {
      day = date.getDate();
    } else {
      re = /(\d+)\.(\d+)\.(\d+)/;
      day = re.exec(date)[1];
    }
    return day;
  },
  formatMonth: function(date) {
    var month, re;
    if (date.length === 0) {
      return;
    }
    if (date.getMonth) {
      month = date.getMonth();
    } else {
      re = /(\d+)\.(\d+)\.(\d+)/;
      month = re.exec(date)[2] - 1;
    }
    return SHORT_MONTHS[month];
  },
  formatDayMonth: function(date) {
    var result;
    if (date.length === 0) {
      return;
    }
    result = "";
    result += date.getDate();
    result += " ";
    return result += MONTHS[date.getMonth()];
  },
  formatDayMonthYear: function(date) {
    var result;
    if (date.length === 0) {
      return;
    }
    console.log('dmy', date, date.getYear());
    result = "";
    result += date.getDate();
    result += " ";
    result += MONTHS[date.getMonth()];
    result += " ";
    return result += date.getFullYear();
  },
  formatDayMonthInterval: function(dateStart, dateEnd) {
    var result;
    if (dateStart.length === 0 || dateEnd.length === 0) {
      return;
    }
    result = "с ";
    result += dateStart.getDate();
    if (dateStart.getMonth() !== dateEnd.getMonth()) {
      result += ' ' + MONTHS[dateStart.getMonth()];
    }
    result += ' по ' + dateEnd.getDate();
    return result += ' ' + MONTHS[dateEnd.getMonth()];
  },
  formatDayShortMonth: function(date) {
    var result;
    if (!date || date.length === 0) {
      return;
    }
    result = "";
    result += date.getDate();
    result += " ";
    return result += SHORT_MONTHS[date.getMonth()];
  },
  formatHtmlDayShortMonth: function(date) {
    var result;
    if (!date.getDate) {
      date = date.toDate();
    }
    result = '<span class="f17">';
    result += date.getDate();
    result += "</span><br>";
    return result += SHORT_MONTHS[date.getMonth()];
  },
  formatDayMonthWeekday: function(date) {
    var result;
    console.log("formatDayMonthWeekday", date);
    if (!date.getDate()) {
      date = date.toDate();
    }
    result = "<b>";
    result += date.getDate();
    result += "</b> ";
    result += SHORT_MONTHS[date.getMonth()];
    result += ", ";
    return result += SHORT_WEEKDAYS[date.getDay()];
  },
  formatTime: function(date) {
    var minutes, result;
    result = "";
    result += date.getHours();
    result += ":";
    minutes = date.getMinutes().toString();
    if (minutes.length === 1) {
      minutes = "0" + minutes;
    }
    result += minutes;
    return result;
  },
  formatTimeInMinutes: function(date) {
    var result;
    result = date.getHours() * 60 + date.getMinutes();
    return result;
  },
  formatDuration: function(duration) {
    var all_minutes, hours, minutes;
    all_minutes = duration / 60;
    minutes = all_minutes % 60;
    hours = (all_minutes - minutes) / 60;
    return hours + " ч. " + minutes + " м.";
  }
};

calcOffset = function() {
  var hours, minutes, minutesDiff, sign, x;
  x = new Date();
  minutesDiff = -x.getTimezoneOffset();
  hours = Math.floor(minutesDiff / 60).toString();
  minutes = (minutesDiff % 60).toString();
  if (hours.length === 1) {
    hours = "0" + hours;
  }
  if (minutes.length === 1) {
    minutes = "0" + minutes;
  }
  sign = minutesDiff < 0 ? '-' : '+';
  return sign + hours + ':' + minutes;
};

Utils = {
  tzOffset: calcOffset(),
  implode: function(glue, pieces) {
    if (pieces instanceof Array) {
      return pieces.join(glue);
    } else {
      return pieces;
    }
  },
  inRange: function(value, range) {
    return range.from <= value && value <= range.to;
  },
  fromIso: function(dateIsoString) {
    var initArray;
    if (typeof dateIsoString === 'string') {
      initArray = dateIsoString.split('-');
      return new Date(initArray[0], initArray[1](-1), initArray[2]);
    } else {
      return dateIsoString;
    }
  },
  scrollTo: function(selector, animation, callback) {
    var oPos;
    if (animation == null) {
      animation = true;
    }
    if (callback == null) {
      callback = null;
    }
    if (typeof selector === "string") {
      oPos = $(selector).offset();
    } else if (typeof selector === "object") {
      oPos = $(selector).offset();
    } else {
      oPos = {};
      oPos.top = selector;
    }
    if (oPos) {
      if (animation) {
        if (callback) {
          return $("html,body").animate({
            'scrollTop': oPos.top
          }, 1000, callback);
        } else {
          return $("html,body").animate({
            'scrollTop': oPos.top
          });
        }
      } else {
        return $("html,body").scrollTop(oPos.top);
      }
    }
  },
  wordAfterNum: function(number, oneWord, fourWord, sevenWord) {
    var iModulo, iNum;
    if (sevenWord == null) {
      sevenWord = false;
    }
    if (!sevenWord) {
      sevenWord = fourWord;
    }
    iNum = number % 100;
    if ((4 < iNum && iNum < 21)) {
      return number + ' ' + sevenWord;
    } else {
      iModulo = iNum % 10;
      if (iModulo === 1) {
        return number + ' ' + oneWord;
      } else if ((1 < iModulo && iModulo < 5)) {
        return number + ' ' + fourWord;
      } else {
        return number + ' ' + sevenWord;
      }
    }
  },
  limitTextLenght: function(text, limit) {
    var pos, result, rusCount, subText;
    result = {};
    pos = text.lastIndexOf(' ', limit);
    subText = text.substr(0, pos);
    rusCount = Utils.countRusChars(subText);
    if (rusCount > (limit / 2)) {
      limit = Math.round(limit * 0.84);
    }
    if (text.length > limit) {
      pos = text.lastIndexOf(' ', limit);
      result['startText'] = text.substr(0, pos);
      result['endText'] = text.substr(pos);
      result['isBigText'] = true;
    } else {
      result['isBigText'] = false;
      result['startText'] = text;
      result['endText'] = '';
    }
    return result;
  },
  countRusChars: function(text) {
    var endLen, re, startLen;
    startLen = text.length;
    re = new RegExp('[а-яА-ЯёЁ]', 'gi');
    endLen = (text.replace(re, '')).length;
    return startLen - endLen;
  },
  submitPayment: function(params) {
    var form_html, key, value;
    form_html = '<form id="buy-form" method="GET" action="' + params.url + '" target="payment_frame">';
    delete params.url;
    for (key in params) {
      value = params[key];
      form_html += "<input type=\"hidden\" name=\"" + key + "\" value=\"" + value + "\" />";
    }
    form_html += '</form>';
    return $(form_html).appendTo('body').submit();
  },
  toBuySubmit: function(toBuy) {
    var form_html, index, key, params, value, _i, _len;
    form_html = '<form id="buy-form" method="GET" action="/buy">';
    for (index = _i = 0, _len = toBuy.length; _i < _len; index = ++_i) {
      params = toBuy[index];
      for (key in params) {
        value = params[key];
        key = "item[" + index + "][" + key + "]";
        form_html += "<input type=\"hidden\" name=\"" + key + "\" value=\"" + value + "\" />";
      }
    }
    form_html += '</form>';
    $('body').append(form_html);
    return $('#buy-form').submit();
  },
  formatPrice: function(price) {
    var i, intPrice, j, ret, strPrice, _i, _ref;
    intPrice = parseInt(price);
    strPrice = intPrice.toString();
    ret = "";
    j = 0;
    for (i = _i = _ref = strPrice.length - 1; _ref <= 0 ? _i <= 0 : _i >= 0; i = _ref <= 0 ? ++_i : --_i) {
      if (j !== 0 && j % 3 === 0) {
        ret = ' ' + ret;
      }
      ret = strPrice[i] + ret;
      j++;
    }
    return ret;
  },
  calculateTheDistance: function(lat1, lng1, lat2, lng2) {
    var $ad, $cdelta, $cl1, $cl2, $delta, $dist, $lat1, $lat2, $long1, $long2, $sdelta, $sl1, $sl2, $x, $y;
    $lat1 = lat1 * Math.PI / 180;
    $lat2 = lat2 * Math.PI / 180;
    $long1 = lng1 * Math.PI / 180;
    $long2 = lng2 * Math.PI / 180;
    $cl1 = Math.cos($lat1);
    $cl2 = Math.cos($lat2);
    $sl1 = Math.sin($lat1);
    $sl2 = Math.sin($lat2);
    $delta = $long2 - $long1;
    $cdelta = Math.cos($delta);
    $sdelta = Math.sin($delta);
    $y = Math.sqrt(Math.pow($cl2 * $sdelta, 2) + Math.pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
    $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
    $ad = Math.atan2($y, $x);
    $dist = $ad * 6372795;
    return $dist;
  },
  flashMessage: function(element) {
    var i, l, _results;
    l = 20;
    i = 0;
    _results = [];
    while (i < 10) {
      element.animate({
        "margin-left": "+=" + (l = -l) + "px"
      }, 50);
      _results.push(i++);
    }
    return _results;
  },
  peopleReadable: function(amount) {
    switch (amount) {
      case 1:
        return "за одного";
      case 2:
        return "за двоих";
      case 3:
        return "за троих";
      case 4:
        return "за четверых";
      case 5:
        return "за пятерых";
      case 6:
        return "за шестерых";
      default:
        return "за компанию";
    }
  }
};

exTrim = function(str, charlist) {
  var re;
  charlist = (!charlist ? " s " : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1"));
  re = new RegExp("^[" + charlist + "]+|[" + charlist + "]+$", "g");
  return str.replace(re, "");
};

String.prototype.format = function() {
  var args;
  args = arguments;
  return this.replace(/{(\d+)}/g, function(match, number) {
    if (typeof args[number] !== 'undefined') {
      return args[number];
    } else {
      return match;
    }
  });
};

isEmail = function(email) {
  var emailPatterh;
  emailPatterh = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
  return email.match(emailPattern);
};

waitElement = function(selector, callback) {
  if ($(selector).size()) {
    return callback($(selector));
  } else {
    return setTimeout(function() {
      return waitElement(selector, callback);
    }, 100);
  }
};
