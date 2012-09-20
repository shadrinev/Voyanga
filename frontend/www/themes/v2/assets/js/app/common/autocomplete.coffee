ko.bindingHandlers.autocomplete =
  init: (element, params) ->
    options = params().split(" ")
    $(element).bind "focus", ->
      $(element).change()

    $(element).autocomplete
      serviceUrl: "http://api.voyanga.com/v1/helper/autocomplete/"+options[0] # Страница для обработки запросов автозаполнения
      minChars: 2 # Минимальная длина запроса для срабатывания автозаполнения
      delimiter: /(,|;)\s*/ # Разделитель для нескольких запросов, символ или регулярное выражение
      maxHeight: 400 # Максимальная высота списка подсказок, в пикселях
      width: 300 # Ширина списка
      zIndex: 9999 # z-index списка
      deferRequestBy: 50 # Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
      country: "Yes"
      onSelect: (data, value) -> # Callback функция, срабатывающая на выбор одного из предложенных вариантов,
        console.log data
        console.log value
        $(element).val(value)

  update: (element, params) ->