class TourPanel extends SearchPanel
  constructor: ->
    @template = 'tour-panel-template'
    window.voyanga_debug "TourPanel created"
    super()
    @sp = new TourSearchParams()
    @rooms = @sp.rooms

    @startCity = @sp.startCity
    @startCityReadable = ko.observable ''
    @startCityReadableGen = ko.observable ''
    @startCityReadableAcc = ko.observable ''

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    @calendarValue = ko.computed =>
      twoSelect: false
      from: false

    @formFilled = ko.computed =>
      result = @startCity
      return result

    @maximizedCalendar = ko.computed =>
      @startCity()

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

$(document).on "keyup change", "input.second-path", (e) ->
  firstValue = $(this).val()
  secondEl = $(this).siblings('input.input-path')
  if ((e.keyCode==8) || (firstValue.length<3))
    secondEl.val('')

