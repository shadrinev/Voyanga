class TourPanelSet
  constructor: ->
    window.voyanga_debug 'Init of TourPanelSet'

    @sp = new TourSearchParams()

    @startCity = @sp.startCity
    @startCityReadable = ko.observable ''
    @startCityReadableGen = ko.observable ''
    @startCityReadableAcc = ko.observable ''

    @panels = ko.observableArray [new TourPanel(@sp, 0)]
    @i = 0

  isFirst: =>
    return @i++ == 0

class TourPanel extends SearchPanel
  constructor: (sp, ind) ->
    @template = 'tour-panel-template'
    window.voyanga_debug "TourPanel created"
    super()

    @rooms = sp.rooms
    @roomsView = ko.computed =>
      result = []
      current = []
      for item in @rooms()
        if current.length == 2
          result.push current
        current = []
        current.push item
        result.push current
      return result

    @calendarHidden = true

    @afterRender = () =>
      $ =>
        @rooms()[0].afterRender()

    @addRoom = () =>
      if @rooms().length == 4
        return
      @rooms.push new Roomers()

    @city = sp.destinations()[0].city
    @cityReadable = ko.observable ''
    @cityReadableGen = ko.observable ''
    @cityReadableAcc = ko.observable ''

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    @calendarValue = ko.computed =>
      twoSelect: false
      from: false

    @formFilled = ko.computed =>
      result = @startCity
      return result

    @maximizedCalendar = ko.computed =>
      @city().length>0

    @calendarText = ko.computed =>
      result = "Выберите дату поездки "
      return result

  # calendar handler
  setDate: (values)=>
    if values.length
      @departureDate values[0]

  # FIXME decouple!
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