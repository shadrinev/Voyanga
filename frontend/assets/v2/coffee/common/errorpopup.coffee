ERRORS =
  avia404:
    title: "Перелеты не найдены"
    text: "Перелеты по данному направлению в выбранные дни не найдены. Попробуйте изменить даты в поисковом запросе."
    buttonText: "Перейти на главную"
    onclose: false
  avia500:
    title: "Упс"
    text: "При обработке запроса произошла внутренняя ошибка сервера. Мы работаем над устранением данной неисправности, попробуйте повторить запрос позже."
    buttonText: "Перейти на главную"
    
class ErrorPopup extends GenericPopup
  constructor: (key, params = false, @onclose=false)->
    id = 'errorpopup'
    data = ERRORS[key]
    data.text = data.text.format params
    if !@onclose
      @onclose = data.onclose
    super '#' + id, data, true
    ko.processAllDeferredBindingUpdates()

    SizeBox(id);
    ResizeBox(id);

  close: =>
    super
    if @onclose
      @onclose()
    else
      # goto index
      window.app.navigate window.app.activeModule(), {trigger: true}
