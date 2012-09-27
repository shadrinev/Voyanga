var MONTHS, SHORT_MONTHS, SHORT_WEEKDAYS, Utils, dateUtils;

MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентрября', 'октября', 'ноября', 'декабря'];

SHORT_MONTHS = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

SHORT_WEEKDAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

dateUtils = {
  formatDay: function(date) {
    var day, re;
    if (date.length === 0) {
      return;
    }
    if (date.getDay()) {
      day = date.getDay();
    } else {
      re = /(\d+)\.(\d+)\.(\d+)/;
      day = re.exec(date)[1];
    }
    return day;
  },
  formatMonth: function(date) {
    var month, re;
    console.log('!@#!@#!@#', date);
    if (date.length === 0) {
      return;
    }
    if (date.getMonth()) {
      month = date.getMonth();
    } else {
      re = /(\d+)\.(\d+)\.(\d+)/;
      month = re.exec(date)[2](-1);
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
  formatDayShortMonth: function(date) {
    var result;
    if (date.length === 0) {
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

Utils = {
  inRange: function(value, range) {
    return range.from <= value && value <= range.to;
  },
  fromIso: function(dateIsoString) {
    var initArray;
    if (typeof dateIsoString === 'string') {
      initArray = dateIsoString.split('-');
      return new Date(initArray[0], initArray[1] - 1, initArray[2]);
    } else {
      return dateIsoString;
    }
  }
};
