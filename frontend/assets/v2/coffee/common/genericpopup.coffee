class GenericPopup
  constructor: (@id, data)->
    $('body').prepend('<div id="popupOverlay"></div>')
    el = $($(@id+'-template').html())
    $('body').prepend(el)
    ko.applyBindings({data: data, close:@close}, el[0])
    ko.processAllDeferredBindingUpdates()
    $(window).keyup (e) =>
      if e.keyCode == 27
        @close()

    $('#popupOverlay').click =>
      @close()


  close: =>
    $(window).unbind 'keyup'
    $(@id).remove()
    $('#popupOverlay').remove()
    do btnClosePopUp