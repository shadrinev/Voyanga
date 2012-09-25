ko.bindingHandlers.autocomplete =
  init: (element, valueAccessor) ->
    $(element).bind "focus", ->
      $(element).change()

    $(element).autocomplete
      serviceUrl: "http://api.voyanga.com/v1/helper/autocomplete/" + valueAccessor().source # Страница для обработки запросов автозаполнения
      minChars: 2 # Минимальная длина запроса для срабатывания автозаполнения
      delimiter: /(,|;)\s*/ # Разделитель для нескольких запросов, символ или регулярное выражение
      maxHeight: 400 # Максимальная высота списка подсказок, в пикселях
      zIndex: 9999 # z-index списка
      deferRequestBy: 0 # Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
      delay: 0
      onSelect: (value, data) -> # Callback функция, срабатывающая на выбор одного из предложенных вариантов,
        valueAccessor().iata(data.code)
        valueAccessor().readable(data.name)
        valueAccessor().readableGen(data.nameGen)
        valueAccessor().readableAcc(data.nameAcc)
        $(element).val(data.name)
        $(element).siblings('input.input-path').val(value)
      onActivate: (value, data) ->
        valueAccessor().iata(data.code)
        valueAccessor().readable(data.name)
        valueAccessor().readableGen(data.nameGen)
        valueAccessor().readableAcc(data.nameAcc)
        $(element).val(data.name)
        $(element).siblings('input.input-path').val(value)

    $(element).on "keyup", ->
      if ($(element).val()=='')
        valueAccessor().iata('')
        valueAccessor().readable('')
        valueAccessor().readableGen('')
        valueAccessor().readableAcc('')

  update: (element, valueAccessor) =>
    iataCode = valueAccessor().iata()

    url = (code) ->
      result = 'http://api.voyanga.com/v1/helper/autocomplete/citiesReadable?'
      params = []
      params.push 'codes[0]=' + code
      result += params.join "&"
      window.voyanga_debug "Generated autocomplete city url", result
      return result

    handleResults = (data) ->
      window.voyanga_debug "Ajax request done for ", data[iataCode]
      valueAccessor().readable(data[iataCode].name)
      valueAccessor().readableGen(data[iataCode].nameGen)
      valueAccessor().readableAcc(data[iataCode].nameAcc)
      $(element).val(data[iataCode].name)
      $(element).siblings('input.input-path').val(data[iataCode].label)

    if (iataCode.length>0)
      window.voyanga_debug "Invoking ajax request to get city info ", iataCode
      $.ajax
        url: url iataCode
        dataType: 'jsonp'
        success: handleResults

