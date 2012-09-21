ko.bindingHandlers.autocomplete =
  init: (element, valueAccessor) ->
    $(element).bind "focus", ->
      $(element).change()

    $(element).autocomplete
      serviceUrl: "http://api.misha.voyanga/v1/helper/autocomplete/" + valueAccessor().source # Страница для обработки запросов автозаполнения
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

  update: (element, valueAccessor) ->
    console.log($(element).val())
    console.log(valueAccessor().iata());
