class TourPanelSet
  constructor: ->
    _.extend @, Backbone.Events

    window.voyanga_debug 'Init of TourPanelSet'

    @template = 'tour-panel-template'
    @sp = new TourSearchParams()

    @prevPanel = 'hotels'
    @nextPanel = 'avia'
    @icon = 'constructor-ico'
    @mainLabel = 'Спланируй свое путешествие <img src="/themes/v2/images/saleTitle.png">'
    @indexMode = true

    @startCity = @sp.startCity
    @startCityReadable = ko.observable ''
    @startCityReadableGen = ko.observable ''
    @startCityReadableAcc = ko.observable ''
    @panels = ko.observableArray []
    @activeCity = ko.observable('')
    @sp.calendarActivated = ko.observable(true)
    @calendarText = ko.computed =>
      result = 'Выберите даты пребывания в городе'
      if @activeCity()
        result += ' ' + @activeCity()
      else
        result = 'Введите город'
      result

    @lastPanel = null
    @i = 0
    @addPanel()
    @activeCalendarPanel = ko.observable @panels()[0]

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


    @formFilled = ko.computed =>
      isFilled = @startCity()
      _.each @panels(), (panel) ->
        isFilled = isFilled && panel.formFilled()
      return isFilled

    @formNotFilled = ko.computed =>
      !@formFilled()

  navigateToNewSearch: =>
    if (@formNotFilled())
      return
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
    @sp.destinations.remove(elem.city)
    @panels.remove(elem)
    _.last(@panels()).isLast(true)

  isFirst: =>
    @i == 1

  addPanel: =>
    @sp.destinations.push new DestinationSearchParams()
    if _.last(@panels())
      _.last(@panels()).isLast(false)
      prevPanel = _.last(@panels())
    newPanel = new TourPanel(@sp, @i, @i == 0)
    newPanel.on "tourPanel:showCalendar", (args...) =>
      @activeCity(newPanel.cityReadable())
      @showPanelCalendar(args)
    #need remove focusOut(blur)
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

  showPanelCalendar: (args) =>
    @activeCalendarPanel args[0]
    console.log 'showPanelCalendar', args

  # calendar handler
  setDate: (values) =>
    console.log 'Calendar selected:', values
    if values && values.length
      @activeCalendarPanel().checkIn values[0]
      maxDate = @activeCalendarPanel().checkIn()
      if values.length > 1
        @activeCalendarPanel().checkOut values[1]
        if maxDate < @activeCalendarPanel().checkOut()
          maxDate = @activeCalendarPanel().checkOut()
      if(@activeCalendarPanel().nextSearchPanel() && maxDate > @activeCalendarPanel().nextSearchPanel().checkIn())
        @activeCalendarPanel().nextSearchPanel().checkIn(null)
        @activeCalendarPanel().nextSearchPanel().checkOut(null)


  calendarHidden: =>
    @activeCalendarPanel().calendarHidden()
    
  afterRender: =>
    do resizePanel
    
  beforeRemove: (el) ->
    if $(el).hasClass 'panel'
      $(el).remove()
      do resizePanel
    else
      $(el).remove()

class TourPanel extends SearchPanel
  constructor: (sp, ind, isFirst) ->
    window.voyanga_debug "TourPanel created"
    super(isFirst, true)
    @toggleSubscribers.dispose();
    console.log('try dispose subscribe');
    _.extend @, Backbone.Events

    @hasfocus = ko.observable false
    @sp = sp
    @isLast = ko.observable true
    @peopleSelectorVM = new HotelPeopleSelector sp
    @destinationSp = _.last(sp.destinations());
    @city = @destinationSp.city
    @checkIn = @destinationSp.dateFrom
    @checkOut = @destinationSp.dateTo
    @cityReadable = ko.observable ''
    @cityReadableGen = ko.observable ''
    @cityReadableAcc = ko.observable ''

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

    @hasfocus.subscribe (newValue) =>
      @trigger "tourPanel:hasFocus", @

    @city.subscribe (newValue) =>
      console.log('city changed!!!!!!!!')
      if @sp.calendarActivated()
        @showCalendar()

  handlePanelSubmitToMain: =>
    handlePanelSubmit(false)

  handlePanelSubmit: (onlyHash = true)=>
    console.log('onlyHash', onlyHash)
    if onlyHash
      app.navigate @sp.getHash(), {trigger: true}
    else
      url = '/#' + @sp.getHash()
      if @startParams == url
        # Need save data to server, because get have limit 2048 bytes
        #url += 'oldSelecton/'+encodeURIComponent(JSON.stringify(@selectedParams))
        url += 'eventId/' + @selectedParams.eventId


      console.log('go url', url, 'length', url.length)
      #return
      window.location.href = url

  saveStartParams: ()=>
    url = '/#' + @sp.getHash()
    @startParams = url

  close: ->
    $(document.body).unbind 'mousedown'
    $('.how-many-man .btn').removeClass('active')
    $('.how-many-man .content').removeClass('active')
    $('.how-many-man').find('.popup').removeClass('active')

  showFromCityInput: (panel, event) ->
    event.stopPropagation()
    elem = $('.cityStart').find('.second-path');
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
      el.find(".startInputTo").find("input").focus().select()

  hideFromCityInput: (panel, event) ->
    hideFromCityInput(panel, event)

  showCalendar: =>
    console.log('calendar show trigger')
    $('.calenderWindow').show()
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