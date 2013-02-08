# Tour/avia/hotels slider in header
#

class Slider
  constructor: ->
    @speed = 400

    @plannerWidth = [143, 0]
    @aviaticketsWidth = [135, 155]
    @hotelWidth = [95, 295]
    @finishStagesWidth = [148, 403]


  init: =>
    activeLI = $('.slide-turn-mode ul').find('.active')
    activeLIindex = activeLI.index()
    if activeLIindex == -1
      activeLIindex = 1

    console.log "activeLIindex", activeLIindex

    @switchSlide = $('.slide-turn-mode').find('.switch')
    @valueWidth = [@plannerWidth, @aviaticketsWidth, @hotelWidth, @finishStagesWidth]

    @switchSlide.css('width', @valueWidth[activeLIindex][0] + 'px').css('left', @valueWidth[activeLIindex][1] + 'px')
    @switchSlide.find('.c').css('width', (@valueWidth[activeLIindex][0] - 27) + 'px')


  click: (scope, event)=>
    event.preventDefault()
    if window.location.href.replace('/','') != ''
      $('#loadWrapBgMin').show()
      window.location.href =  $(event.currentTarget).find('a').attr('href')
      return
    window.app.navigate $(event.currentTarget).find('a').attr('href'), {'trigger': true}

  # handles app.activeModule changes
  handler: (newValue)=>
    if newValue is `undefined`
      return
    console.log "HANDLER: ", newValue
    activeLI = $('#h-' + newValue + '-slider')
    activeLIindex = activeLI.index()
    $('.btn').removeClass 'active'

    @switchSlide.animate
      width : @valueWidth[activeLIindex][0] + 'px'
      left : @valueWidth[activeLIindex][1] + 'px'
      , @speed , ->
        activeLI.addClass('active')
    @switchSlide.find('.c').animate
      width : (@valueWidth[activeLIindex][0] - 27) + 'px'
      , @speed
