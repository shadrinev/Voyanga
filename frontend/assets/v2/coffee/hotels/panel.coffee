class HotelsPanel extends SearchPanel
  constructor: ->
    @template = 'hotels-panel-template'
    super()

    @prevPanel = 'avia'
    @prevPanelLabel = 'Бронирование авиабилетов'
    @nextPanel = 'tours'
    @nextPanelLabel = 'Поиск тура'
    @icon = 'hotel-ico'

    @mainLabel = 'Бронирование отелей'
    @indexMode = ko.observable true

    @sp = new HotelsSearchParams()
    @calendarHidden = ko.observable true
    @city = @sp.city
    @checkIn = @sp.checkIn
    @checkOut = @sp.checkOut
    @peopleSelectorVM = new HotelPeopleSelector @sp
    @cityReadable = ko.observable()
    @cityReadableAcc = ko.observable()
    @cityReadableGen = ko.observable()
    @cityReadablePre = ko.observable()
    @selectionIndex = ko.observable ''
    @calendarActive = ko.observable(true)
    @calendarText = ko.computed =>
      result = 'Введите город'
      if @city()
        if @selectionIndex() == 0
          result = 'Выберите дату приезда в ' + @cityReadableAcc()
        else if @selectionIndex() == 1
          result = 'Выберите дату отъезда из ' + @cityReadableGen()
        else if @selectionIndex() == 2
          result = @cityReadable() + ', ' + dateUtils.formatDayShortMonth(@checkIn()) + ' - ' + dateUtils.formatDayShortMonth(@checkOut())
      result

    @prefixText = "Выберите город<br>200 000+ отелей"

    $('div.innerCalendar').find('h1').removeClass('highlight')

    @formFilled = ko.computed =>
      if @checkIn().getDay
        cin = true
      else
        cin =(@checkIn().length > 0)
      if @checkOut().getDay
        cout = true
      else
        cout =(@checkOut().length > 0)

      result = @city() && cin && cout
      return result

    @formNotFilled = ko.computed =>
      !@formFilled()

    @maximizedCalendar = ko.computed =>
      (@city().length > 0) && (!_.isObject(@checkIn()))

    @maximizedCalendar.subscribe (newValue) =>
      if @calendarActive()
        if !newValue
          return
        @showCalendar()

    @calendarValue = ko.computed =>
      twoSelect: true
      hotels: true
      from: @checkIn()
      to: @checkOut()
      activeSearchPanel: @
      valuesDescriptions: [('Заезд в отель<div class="breakWord">в ' + @cityReadablePre() + '</div>'), ('Выезд из отеля<div class="breakWord">в ' + @cityReadablePre() + '</div>')]
      intervalDescription: '0'
      selectionIndex: @selectionIndex

  handlePanelSubmit: =>
    if window.location.pathname.replace('/', '') != ''
      $('#loadWrapBgMin').show()
      window.location.href = '/#' + @sp.getHash()
      return
    app.navigate @sp.getHash(), {trigger: true}
    @minimizedCalendar(true)

  checkInHtml: =>
    if @checkIn()
      return dateUtils.formatHtmlDayShortMonth @checkIn()
    return ''

  checkOutHtml: =>
    if @checkOut()
      return dateUtils.formatHtmlDayShortMonth @checkOut()
    return ''

  haveDates: =>
    return @checkOut() && @checkIn()

  # FIXME decouple!
  navigateToNewSearch: ->
    if (@formNotFilled())
      el = $('div.innerCalendar').find('h1')
      Utils.flashMessage el
      return
    @handlePanelSubmit()
    @minimizedCalendar(true)


  setDate: (values)=>
    if values.length
      if !@checkIn() || (moment(@checkIn()).format('YYYY-MM-DD') != moment(values[0]).format('YYYY-MM-DD'))
        @checkIn values[0]
      if values.length > 1
        if values[1] > @checkIn()
          if !@checkOut() || (moment(@checkOut()).format('YYYY-MM-DD') != moment(values[1]).format('YYYY-MM-DD'))
            @checkOut values[1]
        else
          @checkOut ''

  afterRender: =>
    do resizePanel
    if (@city() && (!@checkIn() || !@checkOut()))
      @minimizedCalendar(false)

