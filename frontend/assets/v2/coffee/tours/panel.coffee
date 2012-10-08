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

    @height = ko.computed =>
      70 * @panels().length + 'px'

    @isMaxReached = ko.computed =>
      @panels().length > 6

  deletePanel: (elem) =>
    @sp.destinations.remove(elem.city)
    @panels.remove(elem)
    _.last(@panels()).isLast(true)

  isFirst: =>
    @i++ == 0

  addPanel: =>
    @sp.destinations.push new DestinationSearchParams()
    _.last(@panels()).isLast(false)
    @panels.push new TourPanel(@sp, @i)

class TourPanel extends SearchPanel
  constructor: (sp, ind) ->
    @template = 'tour-panel-template'
    window.voyanga_debug "TourPanel created"
    super()

    @isLast = ko.observable true
    @peopleSelectorVM = new HotelPeopleSelector sp
    @city = _.last(sp.destinations()).city
    @dateFrom = _.last(sp.destinations()).dateFrom
    @dateTo = _.last(sp.destinations()).dateTo
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
      @dateFrom values[0]

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