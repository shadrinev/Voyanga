class GenericPopup
  constructor: (@id, data, @modal=false)->
    @inside = false
    $('body').prepend('<div id="popupOverlay"></div>')
    el = $(@id+'-template')
    if el[0] == undefined
      throw "Wrong popup template"
      return
    el = $(el.html())
    
    $('body').prepend(el)
    ko.applyBindings({data: data, close:@close}, el[0])
    ko.processAllDeferredBindingUpdates()
    if @modal
      return
    $(window).keyup (e) =>
      if e.keyCode == 27
        @close()
    $(el.find('table')[0]).hover =>
      @inside = true
    , =>
      @inside = false
    el.click =>
      if !@inside
        @close()


  close: =>
    if !@modal
      $(window).unbind 'keyup'
    $(@id).remove()
    $('#popupOverlay').remove()
    do btnClosePopUp
