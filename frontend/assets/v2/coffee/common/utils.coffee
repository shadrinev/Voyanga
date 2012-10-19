MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
  'июля', 'августа', 'сентрября', 'октября','ноября', 'декабря']

SHORT_MONTHS = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл',
  'авг', 'сен', 'окт', 'ноя', 'дек']

SHORT_WEEKDAYS = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']

dateUtils=
  formatDay: (date) ->
    if date.length==0
      return
    if date.getDay
      day = date.getDate()
    else
      re = /(\d+)\.(\d+)\.(\d+)/
      day = re.exec(date)[1]
    day

  formatMonth: (date) ->
    if date.length==0
      return

    if date.getMonth
      month = date.getMonth()
    else
      re = /(\d+)\.(\d+)\.(\d+)/
      month = re.exec(date)[2] - 1
    SHORT_MONTHS[month]

  formatDayMonth: (date) ->
    if (date.length==0)
      return
    result = ""
    result+= date.getDate()
    result+= " "
    result+= MONTHS[date.getMonth()]

  formatDayMonthInterval: (dateStart,dateEnd) ->
    if (dateStart.length==0 || dateEnd.length==0)
      return
    result = "с "
    result+= dateStart.getDate()
    if dateStart.getMonth() != dateEnd.getMonth()
      result+= ' '+MONTHS[dateStart.getMonth()]
    result+= ' по ' + dateEnd.getDate()
    result+= ' '+MONTHS[dateEnd.getMonth()]

  formatDayShortMonth: (date) ->
    if (date.length==0)
      return
    result = ""
    result+= date.getDate()
    result+= " "
    result+= SHORT_MONTHS[date.getMonth()]

  formatHtmlDayShortMonth: (date) ->
    if !date.getDate
      #moment.js date
      date = date.toDate()
    result = '<span class="f17">'
    result+= date.getDate()
    result+= "</span><br>"
    result+= SHORT_MONTHS[date.getMonth()]

  formatDayMonthWeekday: (date) ->
    console.log "formatDayMonthWeekday", date
    if !date.getDate()
      #moment.js date
      date = date.toDate()
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

  scrollTo: (selector, animation = true)->
    if typeof(selector) == "string"
      oPos = $(selector).offset()
    else
      oPos = {}
      oPos.top = selector
    if animation
      $("html,body").animate({'scrollTop':oPos.top})
    else
      $("html,body").scrollTop(oPos.top)

  wordAfterNum: (number,oneWord,fourWord,sevenWord = false) ->
    if !sevenWord
      sevenWord = fourWord
    iNum = number % 100
    if 4 < iNum < 21
      return number + ' ' + sevenWord
    else
      iModulo = iNum % 10
      if iModulo == 1
        return number + ' ' + oneWord
      else if 1 < iModulo < 5
        return number + ' ' + fourWord
      else
        return number + ' ' + sevenWord

  limitTextLenght: (text,limit) ->
    result = {}
    pos = text.lastIndexOf(' ',limit)
    subText = text.substr(0,pos)
    rusCount = Utils.countRusChars(subText)
    if rusCount > (limit / 2)
      limit = Math.round(limit * 0.84)

    if text.length > limit

      pos = text.lastIndexOf(' ',limit)
      result['startText'] = text.substr(0,pos)
      result['endText'] = text.substr(pos)
      result['isBigText'] = true
    else
      result['isBigText'] = false
      result['startText'] = text
      result['endText'] = ''
    return result

  countRusChars: (text)->
    startLen = text.length
    re = new RegExp('[а-яА-ЯёЁ]','gi')
    endLen = (text.replace(re,'')).length
    return startLen - endLen

  toBuySubmit: (toBuy) ->
    form_html = '<form id="buy-form" method="POST" action="/buy">'
    for params, index in toBuy
      for key,value of params
        key = "item[#{index}][#{key}]"
        form_html += "<input type=\"hidden\" name=\"#{key}\" value=\"#{value}\" />"
    form_html += '</form>'
    $('body').append(form_html)
    $('#buy-form').submit()



exTrim = (str, charlist) ->
  charlist = (if not charlist then " s " else charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1"))
  re = new RegExp("^[" + charlist + "]+|[" + charlist + "]+$", "g")
  str.replace re, ""