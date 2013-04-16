class TourPanelSet
  constructor: ->
    _.extend @, Backbone.Events
    @template = 'tour-panel-template'
    @sp = new TourSearchParams()

    @prevPanel = 'hotels'
    @prevPanelLabel = 'Только отели'
    @nextPanel = 'avia'
    @nextPanelLabel = 'Только авиабилеты'
    @icon = 'constructor-ico'
    @mainLabel = 'Путешествие: авиабилет + отель <img src="/themes/v2/images/saleTitle.png">'
    @indexMode = true

    @startCity = @sp.getStartCity()
    @startCityReadable = ko.observable ''
    @startCityReadableGen = ko.observable ''
    @startCityReadableAcc = ko.observable ''
    @startCityReadablePre = ko.observable ''
    @selectionIndex = ko.observable ''
    @selectionIndex.subscribe(
      (newValue)=>
        if(newValue == 1)
          VoyangaCalendarStandart.checkCalendarValue(false)
          @activeCalendarPanel().checkIn('')
          @activeCalendarPanel().checkOut('')
    )
    @startCityReadableAccShort = ko.computed =>
      if @startCityReadableAcc().length > 15
        result = '<div class="backCity">' + @startCityReadableAcc() + '</div>'
      else
        result = @startCityReadableAcc()
      result

    @returnBack = @sp.returnBack
    @returnBackLabel = ko.computed =>
      'И вернуться в<br/>' + @startCityReadableAccShort()
    @panels = ko.observableArray []
    @activeCity = ko.observable('')
    @activeCityAcc = ko.observable('')
    @activeCityGen = ko.observable('')
    @calendarActivated = ko.observable(true)

    @lastPanel = null
    @i = 0
    @activeCalendarPanel = ko.observable @panels()[0]
    @addPanel()

    @calendarText = ko.computed =>
      result = 'Введите город'
      if @activeCalendarPanel().cityReadable()
        if (@selectionIndex() == 0)
          result = 'Выберите дату приезда в город ' + @activeCalendarPanel().cityReadable()
        else if (@selectionIndex() == 1)
          result = 'Выберите дату отъезда из города ' + @activeCalendarPanel().cityReadable()
        else if @selectionIndex() == 2
          result = @activeCalendarPanel().cityReadable() + ', ' + dateUtils.formatDayShortMonth(@activeCalendarPanel().checkIn()) + ' - ' + dateUtils.formatDayShortMonth(@activeCalendarPanel().checkOut())
      result

    @height = ko.computed =>
      64 * @panels().length + 'px'

    @heightPanelSet = ko.computed =>
      64 * @panels().length

    @isMaxReached = ko.computed =>
      @panels().length > 4

    @calendarValue = ko.computed =>
      twoSelect: true
      hotels: true
      from: @activeCalendarPanel().checkIn()
      to: @activeCalendarPanel().checkOut()
      activeSearchPanel: @activeCalendarPanel()
      valuesDescriptions: [('Заезд в отель<div class="breakWord">в ' + @activeCalendarPanel().cityReadablePre() + '</div>'), ('Выезд из отеля<div class="breakWord">в ' + @activeCalendarPanel().cityReadablePre() + '</div>')]
      intervalDescription: '0'
      selectionIndex: @selectionIndex

    @calendarValue.subscribe =>
      if !VoyangaCalendarStandart.checkCalendarValue()
        window.setTimeout(
          =>
            VoyangaCalendarStandart.checkCalendarValue(true)
          , 100
        )

    @formFilled = ko.computed =>
      isFilled = @startCity()
      _.each @panels(), (panel) ->
        isFilled = isFilled && panel.formFilled()
      return isFilled

    @formNotFilled = ko.computed =>
      !@formFilled()

  navigateToNewSearch: =>
    if (@formNotFilled())
      el = $('div.innerCalendar').find('h1')
      Utils.flashMessage el
      return

    sp = _.last(@panels()).sp
    _.last(@panels()).handlePanelSubmit()
    _.last(@panels()).minimizedCalendar(true)

  navigateToNewSearchMainPage: =>
    if (@formNotFilled())
      return
    if @selectedParams
      _.last(@panels()).selectedParams = @selectedParams
    _.last(@panels()).handlePanelSubmit(false)

  saveStartParams: =>
    _.last(@panels()).saveStartParams()

  deletePanel: (elem) =>
    index = @panels.indexOf(elem)
    @panels.remove(elem)
    @sp.removeDestination(index, 1)
    _.last(@panels()).isLast(true)

  isFirst: =>
    @i == 1

  addPanel: (force = false)=>
    if (force != true) && (@panels().length > 0) && (@selectionIndex() != 2)
      if ((@panels().length == 1) && (@panels()[0].city().length == 0))
        el = $('div.from')
        $(el).find('.second-path').attr('placeholder', 'Введите первый город')
        _.delay ()->
          $('.second-path.tour').last().focus()
        , 200
        Utils.flashMessage el
      else
        el = $('div.innerCalendar').find('h1')
        Utils.flashMessage el
      return
    @sp.addDestination()
    if _.last(@panels())
      _.last(@panels()).isLast(false)
      prevPanel = _.last(@panels())
    newPanel = new TourPanel(@sp, @i, @i == 0, @calendarActivated)
    newPanel.on "tourPanel:showCalendar", (args...) =>
      @activeCity(newPanel.cityReadable())
      @activeCityAcc(newPanel.cityReadableAcc())
      @activeCityGen(newPanel.cityReadableGen())
      @showPanelCalendar(args)

    newPanel.on "tourPanel:hasFocus", (args...) =>
      @activeCity(newPanel.cityReadable())
      @showPanelCalendar(args)
    if prevPanel
      newPanel.prevSearchPanel(prevPanel)
      prevPanel.nextSearchPanel(newPanel)
    @panels.push newPanel
    @lastPanel = newPanel
    @i = @panels().length
    VoyangaCalendarStandart.clear()
    @activeCity('')
    @activeCityAcc('')
    @activeCityGen('')
    @activeCalendarPanel(newPanel)

  showPanelCalendar: (args) =>
    @activeCalendarPanel args[0]
    if ((@activeCity()) && ((!@activeCalendarPanel().checkIn()) || (!@activeCalendarPanel().checkOut())))
      @activeCalendarPanel().showCalendar(false)

  # calendar handler
  setDate: (values) =>
    if values && values.length
      if !@activeCalendarPanel().checkIn() || (moment(@activeCalendarPanel().checkIn()).format('YYYY-MM-DD') != moment(values[0]).format('YYYY-MM-DD'))
        @activeCalendarPanel().checkIn values[0]
      maxDate = @activeCalendarPanel().checkIn()
      if values.length > 1
        if (@activeCalendarPanel().checkIn() < values[1])
          if !@activeCalendarPanel().checkOut() || (moment(@activeCalendarPanel().checkOut()).format('YYYY-MM-DD') != moment(values[1]).format('YYYY-MM-DD'))
            @activeCalendarPanel().checkOut values[1]
        else
          @activeCalendarPanel().checkOut ''
        if maxDate < @activeCalendarPanel().checkOut()
          maxDate = @activeCalendarPanel().checkOut()
      if(@activeCalendarPanel().nextSearchPanel() && maxDate > @activeCalendarPanel().nextSearchPanel().checkIn())
        @activeCalendarPanel().nextSearchPanel().checkIn(null)
        @activeCalendarPanel().nextSearchPanel().checkOut(null)


  calendarHidden: =>
    @activeCalendarPanel().calendarHidden()

  afterRender: (el) =>
    do resizePanel
    if ((@activeCity()) && ((!@activeCalendarPanel().checkIn()) || (!@activeCalendarPanel().checkOut())))
      @activeCalendarPanel().showCalendar(false)

  beforeRemove: (el) ->
    if $(el).hasClass 'panel'
      $(el).remove()
      do resizePanel
    else
      $(el).remove()

class TourPanel extends SearchPanel
  constructor: (sp, ind, isFirst, @calendarActivated) ->
    super(isFirst, true)
    @toggleSubscribers.dispose()
    _.extend @, Backbone.Events

    @hasfocus = ko.observable false
    # FIXME может побрекать по параметрам на панельку?
    @sp = sp
    @isLast = ko.observable true
    @peopleSelectorVM = new HotelPeopleSelector sp.getRoomsContainer()
    @destinationSp = sp.getLastDestination()

    @city = @destinationSp.city
    @checkIn = @destinationSp.dateFrom
    @checkOut = @destinationSp.dateTo
    @cityReadable = ko.observable ''
    @cityReadableGen = ko.observable ''
    @cityReadableAcc = ko.observable ''
    @cityReadablePre = ko.observable ''

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    @formFilled = ko.computed =>
      @city() && @checkIn() && @checkOut()

    @formNotFilled = ko.computed =>
      !@formFilled()

    @maximizedCalendar = ko.computed =>
      @city().length > 0

    @calendarText = ko.computed =>
      result = "Выберите дату поездки "
      return result

    $(document).on 'focus', '.second-path.tour', (e)=>
      $(e.target).closest('.panel').css('z-index','5000').siblings('.panel').css('z-index', '2000');
      @trigger "tourPanel:hasFocus", ko.contextFor(e.target)['$data']

    @city.subscribe (newValue) =>
      if @calendarActivated()
        @showCalendar()

  handlePanelSubmitToMain: =>
    handlePanelSubmit(false)

  handlePanelSubmit: (onlyHash = true)=>
    if onlyHash
      app.navigate @sp.hash(), {trigger: true}
    else
      url = '/#' + @sp.hash()
      if @startParams == url
        if @selectedParams.eventId
          url += 'eventId/' + @selectedParams.eventId + '/'
        else if @selectedParams.orderId
          url += 'orderId/' + @selectedParams.orderId + '/'
      window.location.href = url

  saveStartParams: ()=>
    url = '/#' + @sp.hash()
    @startParams = url

  close: ->
    $(document.body).unbind 'mousedown'
    $('.how-many-man .btn').removeClass('active')
    $('.how-many-man .content').removeClass('active')
    $('.how-many-man').find('.popup').removeClass('active')

  showFromCityInput: (panel, event) ->
    event.stopPropagation()
    elem = $('.cityStart').find('.second-path')

    elem.data('old', elem.val())
    el = elem.closest('.cityStart')
    el.closest('.tdCityStart')
    .animate({
    width: '+=130', 300
    })
    el.closest('.tdCityStart').find('.bgInput')
    .animate({
    width: '+=150', 300
    })
    el.closest('.tdCityStart').next().find('.data')
    .animate({
    width: '-=130', 300
    })
    el.find(".startInputTo").show()
    el.find('.cityStart').animate
      width: "261px"
      , 300, ->
        el.find(".startInputTo").find("input").focus()

  hideFromCityInput: (panel, event) ->
    hideFromCityInput(panel, event)

  showCalendar: (trig = true) =>
    ch = $('.calenderWindow').height()
    if(ch == 0)
      $('.calenderWindow').show()
      window.setTimeout(
        ->
          VoyangaCalendarStandart.scrollToDate(VoyangaCalendarStandart.scrollDate, true)
        ,50
      )

      if trig
        @trigger "tourPanel:showCalendar", @
      if @minimizedCalendar()
        ResizeAvia()
        @minimizedCalendar(false)

  checkInHtml: =>
    if @checkIn()
      return dateUtils.formatHtmlDayShortMonth @checkIn()
    return ''

  checkOutHtml: =>
    if @checkOut()
      return dateUtils.formatHtmlDayShortMonth @checkOut()
    return ''

$(document).on "keyup change", "input.second-path", (e) ->
  firstValue = $(this).val()
  secondEl = $(this).siblings('input.input-path')
  if ((e.keyCode == 8) || (firstValue.length < 3))
    secondEl.val('')

$(document).on "keyup change", '.cityStart input.second-path', (e) ->
  elem = $('.from.active .second-path')
  if (e.keyCode == 13)
    if elem.parent().hasClass("overflow")
      elem.parent().animate
        width: "271px"
      , 300, ->
        $(this).removeClass "overflow"
        $('.from.active .second-path').focus()

      $(".cityStart").animate
        width: "115px"
      , 300
      $(".cityStart").find(".startInputTo").animate
        opacity: "1"
      , 300, ->
        $(this).hide()