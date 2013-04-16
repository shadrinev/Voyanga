AVIA_TICKET_TIMELIMIT = 15 * 60
HOTEL_TICKET_TIMELIMIT = 15 * 60
TOURS_TICKET_TIMELIMIT = 15 * 60

MONTHS = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
  'июля', 'августа', 'сентрября', 'октября', 'ноября', 'декабря']

ACC_MONTHS = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь',
  'июль', 'август', 'сентрябрь', 'октябрь', 'ноябрь', 'декабрь']

SHORT_MONTHS = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл',
  'авг', 'сен', 'окт', 'ноя', 'дек']

SHORT_WEEKDAYS = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']

dateUtils =
  formatDay: (date) ->
    if date.length == 0
      return
    if date.getDay
      day = date.getDate()
    else
      re = /(\d+)\.(\d+)\.(\d+)/
      day = re.exec(date)[1]
    day

  formatMonth: (date) ->
    if date.length == 0
      return

    if date.getMonth
      month = date.getMonth()
    else
      re = /(\d+)\.(\d+)\.(\d+)/
      month = re.exec(date)[2] - 1
    SHORT_MONTHS[month]

  formatDayMonth: (date) ->
    if (date.length == 0)
      return
    result = ""
    result += date.getDate()
    result += " "
    result += MONTHS[date.getMonth()]

  formatDayMonthYear: (date) ->
    if (date.length == 0)
      return
    result = ""
    result += date.getDate()
    result += " "
    result += MONTHS[date.getMonth()]
    result += " "
    result += date.getFullYear()

  formatDayMonthInterval: (dateStart, dateEnd) ->
    if (dateStart.length == 0 || dateEnd.length == 0)
      return
    result = "с "
    result += dateStart.getDate()
    if dateStart.getMonth() != dateEnd.getMonth()
      result += ' ' + MONTHS[dateStart.getMonth()]
    result += ' по ' + dateEnd.getDate()
    result += ' ' + MONTHS[dateEnd.getMonth()]

  formatDayShortMonth: (date) ->
    if (!date || date.length == 0)
      return
    result = ""
    result += date.getDate()
    result += " "
    result += SHORT_MONTHS[date.getMonth()]

  formatHtmlDayShortMonth: (date) ->
    if !date.getDate
      #moment.js date
      date = date.toDate()
    result = '<span class="f17">'
    result += date.getDate()
    result += "</span><br>"
    result += SHORT_MONTHS[date.getMonth()]

  formatDayMonthWeekday: (date) ->
    if !date.getDate()
      #moment.js date
      date = date.toDate()
    result = "<b>"
    result += date.getDate()
    result += "</b> "
    result += SHORT_MONTHS[date.getMonth()]
    result += ", "
    result += SHORT_WEEKDAYS[date.getDay()]

  formatTime: (date) ->
    result = ""
    result += date.getHours()
    result += ":"
    minutes = date.getMinutes().toString()
    if minutes.length == 1
      minutes = "0" + minutes
    result += minutes
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

calcOffset = ->
  x = new Date()
  minutesDiff = -x.getTimezoneOffset()
  hours = Math.floor(minutesDiff / 60).toString()
  minutes = (minutesDiff % 60).toString()

  if hours.length == 1
    hours = "0" + hours

  if minutes.length == 1
    minutes = "0" + minutes
  sign = if minutesDiff < 0 then '-' else '+'
  sign + hours + ':' + minutes


Utils =
  tzOffset: calcOffset()
  implode: (glue, pieces) ->
    (if (pieces instanceof Array) then pieces.join(glue) else pieces)

  inRange: (value, range) ->
    range.from <= value && value <= range.to

  fromIso: (dateIsoString) ->
    if typeof dateIsoString == 'string'
      initArray = dateIsoString.split('-')
      return new Date(initArray[0], (initArray[1] -1), initArray[2])
    else
      return dateIsoString

  scrollTo: (selector, animation = true, callback = null)->
    if typeof(selector) == "string"
      oPos = $(selector).offset()
    else if typeof(selector) == "object"
      oPos = $(selector).offset()
    else
      oPos = {}
      oPos.top = selector
    if oPos
      if animation
        if callback
          $("html,body").animate({'scrollTop': oPos.top}, 1000, callback)
        else
          $("html,body").animate({'scrollTop': oPos.top})

      else
        $("html,body").scrollTop(oPos.top)

  wordAfterNum: (number, oneWord, fourWord, sevenWord = false) ->
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

  limitTextLenght: (text, limit) ->
    result = {}
    pos = text.lastIndexOf(' ', limit)
    subText = text.substr(0, pos)
    rusCount = Utils.countRusChars(subText)
    if rusCount > (limit / 2)
      limit = Math.round(limit * 0.84)

    if text.length > limit

      pos = text.lastIndexOf(' ', limit)
      result['startText'] = text.substr(0, pos)
      result['endText'] = text.substr(pos)
      result['isBigText'] = true
    else
      result['isBigText'] = false
      result['startText'] = text
      result['endText'] = ''
    return result

  countRusChars: (text)->
    startLen = text.length
    re = new RegExp('[а-яА-ЯёЁ]', 'gi')
    endLen = (text.replace(re, '')).length
    return startLen - endLen


  submitPayment: (params) ->
    form_html = '<form id="buy-form" method="GET" action="' + params.url + '" target="payment_frame">'
    delete params.url
    for key,value of params
      form_html += "<input type=\"hidden\" name=\"#{key}\" value=\"#{value}\" />"
    form_html += '</form>'
    $(form_html).appendTo('body').submit()

  toBuySubmit: (toBuy) ->
    form_html = '<form id="buy-form" method="GET" action="/buy">'
    for params, index in toBuy
      for key,value of params
        key = "item[#{index}][#{key}]"
        form_html += "<input type=\"hidden\" name=\"#{key}\" value=\"#{value}\" />"
    form_html += '</form>'
    $.cookie "currentTourHash", window.location.hash.substring(1)
    $('body').append(form_html)
    $('#buy-form').submit()

  formatPrice: (price) ->
    intPrice = parseInt(price)
    strPrice = intPrice.toString()
    ret = ""
    j = 0
    for i in [(strPrice.length - 1)..0]
      if j != 0 && j % 3 == 0
        ret = ' ' + ret
      ret = strPrice[i] + ret
      j++
    return ret


  calculateTheDistance: (lat1, lng1, lat2, lng2)=>
    #координаты в радианы
    $lat1 = lat1 * Math.PI / 180
    ;
    $lat2 = lat2 * Math.PI / 180
    ;
    $long1 = lng1 * Math.PI / 180
    ;
    $long2 = lng2 * Math.PI / 180
    ;

    #косинусы и синусы широт и разницы долгот
    $cl1 = Math.cos($lat1)
    ;
    $cl2 = Math.cos($lat2)
    ;
    $sl1 = Math.sin($lat1)
    ;
    $sl2 = Math.sin($lat2)
    ;
    $delta = $long2 - $long1
    ;
    $cdelta = Math.cos($delta)
    ;
    $sdelta = Math.sin($delta)
    ;

    #вычисления длины большого круга
    $y = Math.sqrt(Math.pow($cl2 * $sdelta, 2) + Math.pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2))
    ;
    $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta
    ;
    $ad = Math.atan2($y, $x)
    ;
    #6372795 - Earth radius
    $dist = $ad * 6372795
    ;
    return $dist
    ;

  flashMessage: (element) =>
    l = 20
    i = 0

    while i < 10
      element.animate
        "margin-left": "+=" + (l = -l) + "px"
      , 50
      i++

  peopleReadable: (amount) ->
    switch amount
      when 1 then "за одного"
      when 2 then "за двоих"
      when 3 then "за троих"
      when 4 then "за четверых"
      when 5 then "за пятерых"
      when 6 then "за шестерых"
      else "за компанию"

  animationCascade: (paramsArray, level = 0) ->
    sizeCascade = paramsArray.length
    levelParams = paramsArray[level]
    obj = levelParams['object']
    props = levelParams['propeties']
    opts = levelParams['options']
    haveFunc = false
    if opts['complete']
      haveFunc = true
      func = opts['complete']
    opts['complete'] = ->
      if haveFunc
        func()
      if level < (sizeCascade-1)
        Utils.animationCascade(paramsArray,(level+1))
    obj.animate(props,opts)

exTrim = (str, charlist) ->
  charlist = (if not charlist then " s " else charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1"))
  re = new RegExp("^[" + charlist + "]+|[" + charlist + "]+$", "g")
  str.replace re, ""


String.prototype.format = ->
  args = arguments
  ;
  @replace /{(\d+)}/g, (match, number) ->
    if typeof args[number] != 'undefined' then  args[number] else match

isEmail = (email) ->
  emailPatterh = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
  email.match emailPattern


waitElement = (selector, callback) ->
  if $(selector).size()
    return callback($(selector))
  else
    setTimeout ->
      waitElement(selector, callback)
    , 100