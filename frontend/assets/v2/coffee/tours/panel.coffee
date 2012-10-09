class TourPanelSet
  constructor: ->
    _.extend @, Backbone.Events

    window.voyanga_debug 'Init of TourPanelSet'

    @template = 'tour-panel-template'
    @sp = new TourSearchParams()

    @startCity = @sp.startCity
    @startCityReadable = ko.observable ''
    @startCityReadableGen = ko.observable ''
    @startCityReadableAcc = ko.observable ''
    @panels = ko.observableArray []
    @i = 0
    @addPanel()
    @activeCalendarPanel = @panels()[0]
    @checkIn = @activeCalendarPanel.checkIn
    @checkOut = @activeCalendarPanel.checkOut

    @height = ko.computed =>
      70 * @panels().length + 'px'

    @isMaxReached = ko.computed =>
      @panels().length > 6

    @calendarValue = ko.computed =>
      twoSelect: true
      hotels: true
      from: @checkIn()
      to: @checkOut()

    @formFilled = ko.computed =>
      isFilled = true

      _.each @panels(), (panel) ->
        isFilled &&= panel.formFilled()

      console.log 'IS FILLED ', isFilled

      result = @startCity && isFilled
      return result

  deletePanel: (elem) =>
    @sp.destinations.remove(elem.city)
    @panels.remove(elem)
    _.last(@panels()).isLast(true)

  isFirst: =>
    @i++ == 0

  addPanel: =>
    @sp.destinations.push new DestinationSearchParams()
    if _.last(@panels())
      _.last(@panels()).isLast(false)
    newPanel = new TourPanel(@sp, @i, @i==0)
    newPanel.on "tourPanel:showCalendar", (args...) =>
      @showPanelCalendar(args)
    @panels.push newPanel
    VoyangaCalendarStandart.clear()

  showPanelCalendar: (args) =>
    @activeCalendarPanel = args[0]
    console.log 'showPanelCalendar', args

  # calendar handler
  setDate: (values) =>
    console.log 'Calendar selected:', values
    if (values)
      @activeCalendarPanel.checkIn(values[0])
      if (values[1])
        @activeCalendarPanel.checkOut(values[1])

class TourPanel extends SearchPanel
  constructor: (sp, ind, isFirst) ->
    window.voyanga_debug "TourPanel created"
    super(isFirst)

    _.extend @, Backbone.Events

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
      result = @city() && @checkIn() && @checkOut()
      return result

    @maximizedCalendar = ko.computed =>
      @city().length > 0

    @calendarText = ko.computed =>
      result = "Выберите дату поездки "
      return result

  handlePanelSubmit: =>
    app.navigate @sp.getHash(), {trigger: true}

  navigateToNewSearch: ->
    @handlePanelSubmit()
    @minimizedCalendar(true)

  close: ->
    $(document.body).unbind 'mousedown'
    $('.how-many-man .btn').removeClass('active')
    $('.how-many-man .content').removeClass('active')
    $('.how-many-man').find('.popup').removeClass('active')

  showFromCityInput: (panel, event) ->
    elem = $('.cityStart .second-path')
    elem.data('old', elem.val())
    el = elem.closest('.tdCity')
    el.find(".from").addClass("overflow").animate
      width: "125px"
    , 300
    el.find(".startInputTo").show()
    el.find('.cityStart').animate
      width: "261px"
    , 300, ->
      el.find(".startInputTo").find("input").focus().select()

  hideFromCityInput: (panel, event) ->
    elem = $('.from.active .second-path')
    if elem.parent().hasClass("overflow")
      elem.parent().animate
        width: "271px"
      , 300, ->
        $(this).removeClass "overflow"
    
      $(".cityStart").animate
        width: "115px"
      , 300
      $(".cityStart").find(".startInputTo").animate
        opacity: "1"
      , 300, ->
        $(this).hide()

  showCalendar: =>
    console.log "SHOW CALENDAR"
    $('.calenderWindow').show()
    ResizeAvia()
    @trigger "tourPanel:showCalendar", @
    if @minimizedCalendar()
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
  if ((e.keyCode==8) || (firstValue.length<3))
    secondEl.val('')

$(document).on  "keyup change", '.cityStart input.second-path', (e) ->
  elem = $('.from.active .second-path')
  if (e.keyCode==13)
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