var MONTHS, SHORT_MONTHS, SHORT_WEEKDAYS, dateUtils;

MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентрября', 'октября', 'ноября', 'декабря'];

SHORT_MONTHS = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

SHORT_WEEKDAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

dateUtils = {
  formatDayMonth: function(date) {
    var result;
    result = "";
    result += date.getDate();
    result += " ";
    return result += MONTHS[date.getMonth()];
  },
  formatDayMonthWeekday: function(date) {
    var result;
    result = "";
    result += date.getDate();
    result += " ";
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
  formatDuration: function(duration) {
    var all_minutes, hours, minutes;
    all_minutes = duration / 60;
    minutes = all_minutes % 60;
    hours = (all_minutes - minutes) / 60;
    return hours + " ч. " + minutes + " м.";
  }
};
