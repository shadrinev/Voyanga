MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
  'июля', 'августа', 'сентрября', 'октября','ноября', 'декабря']

dateUtils=
  formatDayMonth: (date) ->
    result = ""
    result+= date.getDate()
    result+= " "
    result+= MONTHS[date.getMonth()]
