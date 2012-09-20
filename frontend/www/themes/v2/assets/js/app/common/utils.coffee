MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
  'июля', 'августа', 'сентрября', 'октября','ноября', 'декабря']

SHORT_MONTHS = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл',
  'авг', 'сен', 'окт', 'ноя', 'дек']

SHORT_WEEKDAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']

dateUtils=
  formatDayMonth: (date) ->
    result = ""
    result+= date.getDate()
    result+= " "
    result+= MONTHS[date.getMonth()]

  formatDayShortMonth: (date) ->
    result = ""
    result+= date.getDate()
    result+= " "
    result+= SHORT_MONTHS[date.getMonth()]


  formatDayMonthWeekday: (date) ->
    result = "<b>"
    result+= date.getDate()
    result+= "</b> "
    result+= SHORT_MONTHS[date.getMonth()]
    result+= ", "
    result+= SHORT_WEEKDAYS[date.getDay()]

  formatTime: (date) ->
    result = ""
    result+= date.getHours()
    result+=":"
    minutes = date.getMinutes().toString()
    if minutes.length == 1
      minutes = "0" + minutes
    result+= minutes
    return result

  formatTimeInMinutes: (date) ->
    result = date.getHours() * 60 + date.getMinutes()
    return result


  # Fixme we should rename container or move this to timeUtils
  formatDuration: (duration) ->
    # LOL!
    all_minutes = duration / 60
    minutes = all_minutes % 60
    hours = (all_minutes - minutes) / 60
    hours + " ч. " + minutes + " м."

Utils =
  inRange: (value, range) ->
    range.from <= value && value <= range.to 

  fromIso: (dateIsoString) ->
    if typeof dateIsoString == 'string'
      initArray = dateIsoString.split('-')
      return new Date(initArray[0],(initArray[1]-1),initArray[2])
    else
      return dateIsoString
