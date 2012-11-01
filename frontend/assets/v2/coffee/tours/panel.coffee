class TourPanelSet
  constructor: ->
    _.extend @, Backbone.Events

    window.voyanga_debug 'Init of TourPanelSet'

    @template = 'tour-panel-template'
    @sp = new TourSearchParams()

    @prevPanel = 'hotels'
    @nextPanel = 'avia'
    @icon = 'constructor-ico';
    @indexMode = true

    @startCity = @sp.startCity
    @startCityReadable = ko.observable ''
    @startCityReadableGen = ko.observable ''
    @startCityReadableAcc = ko.observable ''
    @panels = ko.observableArray []
    @i = 0
    @addPanel()
    @activeCalendarPanel = ko.observable @panels()[0]

    @height = ko.computed =>
      64 * @panels().length + 'px'

    @isMaxReached = ko.computed =>
      @panels().length > 6

    @calendarValue = ko.computed =>
      twoSelect: true
      hotels: true
      from: @activeCalendarPanel().checkIn()
      to: @activeCalendarPanel().checkOut()

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
    @i == 1

  addPanel: =>
    @sp.destinations.push new DestinationSearchParams()
    if _.last(@panels())
      _.last(@panels()).isLast(false)
    newPanel = new TourPanel(@sp, @i, @i==0)
    newPanel.on "tourPanel:showCalendar", (args...) =>
      @showPanelCalendar(args)
    newPanel.on "tourPanel:hasFocus", (args...) =>
      @showPanelCalendar(args)
    @panels.push newPanel
    @i = @panels().length
    VoyangaCalendarStandart.clear()

  showPanelCalendar: (args) =>
    VoyangaCalendarStandart.clear()
    @activeCalendarPanel  args[0]
    console.log 'showPanelCalendar', args

  # calendar handler
  setDate: (values) =>
    console.log 'Calendar selected:', values
    if values && values.length
      @activeCalendarPanel().checkIn values[0]
      if values.length > 1
        @activeCalendarPanel().checkOut values[1]

  calendarHidden: =>
#    console.error("HANDLE ME")

class TourPanel extends SearchPanel
  constructor: (sp, ind, isFirst) ->
    window.voyanga_debug "TourPanel created"
    super(isFirst)

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
      result = @city() && @checkIn() && @checkOut()
      return result

    @formNotFilled = ko.computed =>
      !@formFilled()

    @maximizedCalendar = ko.computed =>
      @city().length > 0

    @calendarText = ko.computed =>
      result = "Выберите дату поездки "
      return result

    @hasfocus.subscribe (newValue) =>
      console.log "HAS FOCUS", @
      @trigger "tourPanel:hasFocus", @

    @city.subscribe (newValue) =>
      @showCalendar()

  handlePanelSubmit: =>
    app.navigate @sp.getHash(), {trigger: true}

  navigateToNewSearch: ->
    if (@formNotFilled())
      return
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
    elem = $('.startInputTo .second-path')
    console.log "Hide city input", elem.parent()
    startInput = $('div.startInputTo')
    toInput = $('div.overflow')
    if startInput.is(':visible')
      toInput.animate
        width: "271px"
      , 300, ->
        toInput.removeClass "overflow"
    
      $(".cityStart").animate
        width: "115px"
      , 300
      startInput.animate
        opacity: "1"
      , 300, ->
        startInput.hide()

  showCalendar: =>
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