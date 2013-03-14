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
  aviaNoTicketOnValidation:
    title: "Не подтвердился авиабилет"
    text: "При проверке доступности выбранный авиабилет не подтвердился. Это могло произойти по причине того что билет по данному тарифу уже купил кто-то другой. Попробуйте выбрать другой вариант перелета."
    buttonText: "Ok"

  hotels404:
    title: "Гостиницы не найдены"
    text: "Доступные для бронирования гостиницы в выбранном городе в эти дни не найдены. Попробуйте изменить параметры поискового запроса."
    buttonText: "Перейти на главную"
  hotelsNoTicketOnValidation:
    title: "Не подтвердился выбранный отель"
    text: "При проверке доступности, отель не подтвердил доступность выбранного номера. Это могло произойти по причине того что номер уже забронировал кто-то другой. Попробуйте выбрать другой вариант."
    buttonText: "Ok"
  toursNoTicketOnValidation:
    title: "Не подтвердился выбранный вариант"
    text: "При проверке доступности, некоторые из сегментов не подтвердились: TODO Попробуйте выбрать другой вариант."
    buttonText: "Ok"
  e500withText:
    title: "Упс"
    text: "При обработке запроса произошла внутренняя ошибка сервера. Мы работаем над устранением данной неисправности, попробуйте повторить запрос позже: {0}"
    buttonText: "Перейти на главную"

  e500:
    title: "Упс"
    text: "При обработке запроса произошла внутренняя ошибка сервера. Мы работаем над устранением данной неисправности, попробуйте повторить запрос позже."
    buttonText: "Перейти на главную"

  passport500:
    title: "Упс"
    text: "При обработке запроса произошла внутренняя ошибка сервера. Мы работаем над устранением данной неисправности, попробуйте повторить запрос позже."
    buttonText: "Перейти на главную"

  passportBookingError:
    title: "Ошибка бронирования"
    text: "При выполнении запроса произошла ошибка бронирования следующих сегментов: {0} Это могло произойти по причине того что выбранный вариант уже купил кто-то другой. Попробуйте выбрать что-то другое."
    buttonText: "Ok"

class ErrorPopup extends GenericPopup
  constructor: (key, params = false, @onclose=false)->
    activeModule = window.app.activeModule()
    if activeModule == 'tours'
      activeModule = 'trip'
    if activeModule == 'hotels'
      activeModule = 'hotel'


    id = 'errorpopup'
    data = ERRORS[key]
    data.text = data.text.format params

    cat = 'error-' + activeModule
    _gaq.push(['_trackEvent', 'error-message', cat, data.text.substring(0,200), '', true])

    
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
      window.location = '/#' + (window.app.activeModule()||'tours')
#      window.app.navigate window.app.activeModule(), {trigger: true}
