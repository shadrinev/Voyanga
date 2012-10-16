class GenericPopup
  constructor: (@id, data)->
    @inside = false
    $('body').prepend('<div id="popupOverlay"></div>')
    el = $($(@id+'-template').html())
    $('body').prepend(el)
    ko.applyBindings({data: data, close:@close}, el[0])
    ko.processAllDeferredBindingUpdates()
    $(window).keyup (e) =>
      if e.keyCode == 27
        @close()
    $(el.find('table')[0]).hover =>
      console.log "INSIDE"
      @inside = true
    , =>
      console.log "OUTSIDE"
      @inside = false
    el.click =>
      console.log "CLEAK"
      if !@inside
        @close()


  close: =>
    $(window).unbind 'keyup'
    $(@id).remove()
    $('#popupOverlay').remove()
    do btnClosePopUp