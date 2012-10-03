class SearchPanel
  constructor: ->
    @minimized = ko.observable false
    @minimizedCalendar = ko.observable true
    @calendarHidden = ko.observable @minimizedCalendar
    @calendarShadow = ko.observable @minimizedCalendar

    #helper to save calendar state
    @oldCalendarState = @minimizedCalendar()

    @togglePanel @minimized()
    @toggleCalendar @minimizedCalendar()

    @minimized.subscribe (minimized) =>
      @togglePanel(minimized)

    @minimizedCalendar.subscribe (minimizedCalendar) =>
      @toggleCalendar(minimizedCalendar)


    # helper flags for clicck outside of ppl panel handling
    @inside = false
    @inside2 = false
    @inside3 = false


  afterRender: =>
    $('.how-many-man .popup').find('input').hover ->
      $(this).parent().find('.plusOne').show()
      $(this).parent().find('.minusOne').show()

    $('.adults,.childs,.small-childs').hover null,   ->
      $(this).parent().find('.plusOne').hide()
      $(this).parent().find('.minusOne').hide()

    $('.plusOne').unbind 'hover'
    $('.plusOne').hover ->
      $(this).addClass('active')
      $('.minusOne').addClass('active')
    , ->
      $(this).removeClass('active')
      $('.minusOne').removeClass('active')

    $('.minusOne').unbind 'hover'
    $('.minusOne').hover ->
      $(this).addClass('active');
      $('.plusOne').addClass('active')
    , ->
      $(this).removeClass('active')
      $('.plusOne').removeClass('active')

# I think this is impelemented in valudation code
    # Placeholder-like behaviour for inputs
#    $('.how-many-man .popup').find('input').focus ->
#      $(@).attr 'rel', $(@).val()
#      $(@).val('')

#    $('.how-many-man .popup').find('input').blur ->
#      if $(@).val() == ''
#        $(@).val $(@).attr 'rel'
#      $(@).trigger 'change'

    $('.how-many-man').find('.popup').unbind 'hover'

    $('.how-many-man').find('.popup').hover =>
      @inside = true
    , =>
      @inside = false

    $('.how-many-man .content').unbind 'hover'
    $('.how-many-man .content').hover =>
      @inside2 = true
    , =>
      @inside2 = false
      
    $('.how-many-man .btn').unbind 'hover'
    $('.how-many-man .btn').hover =>
      @inside3 = true
    , =>
      @inside3 = false

  show: (context, event)=>
    el = $(event.currentTarget)
    if !el.hasClass('active')
      $(document.body).mousedown =>
        if @inside ||  @inside2 || @inside3
          return
        @close()
      $('.how-many-man .btn').addClass('active')
      $('.how-many-man .content').addClass('active')
      $('.how-many-man').find('.popup').addClass('active')

    else
      @close()

  close: ->
    $(document.body).unbind 'mousedown'
    $('.how-many-man .btn').removeClass('active')
    $('.how-many-man .content').removeClass('active')

    $('.how-many-man').find('.popup').removeClass('active')
  	

  togglePanel: (minimized) =>
    speed =  300
    heightSubHead = $('.sub-head').height()

    if !minimized
      $('.sub-head').animate {'margin-top' : '0px'}, speed
    else
      $('.sub-head').animate {'margin-top' : '-'+(heightSubHead-4)+'px'}, speed

  toggleCalendar: (minimizedCalendar) =>
    speed =  500

    heightSubHead = $('.sub-head').height()
    heightCalendar1 = $('.calenderWindow').height()
    heightCalendar2 = heightSubHead

    if !minimizedCalendar
      @calendarHidden(false)
      $('.calenderWindow').animate {'top' : (heightSubHead+1) + 'px'}, speed
    else
      @calendarShadow(true)
      $('.calenderWindow').animate {'top' : '-'+(heightCalendar1)+'px'}, speed, () =>
        @calendarShadow(false)

  # Minimize button click handler
  minimize: =>
    if @minimized()
      @minimized(false)
      @minimizedCalendar(@oldCalendarState)
    else
      @minimized(true)
      @oldCalendarState = @minimizedCalendar()
      if !@minimizedCalendar()
        @minimizedCalendar(true)

  # Minimize button click handler
  minimizeCalendar: =>
    if @minimizedCalendar()
      @minimizedCalendar(false)
    else
      @minimizedCalendar(true)

  showCalendar: =>
    if @minimizedCalendar()
      @minimizedCalendar(false)

