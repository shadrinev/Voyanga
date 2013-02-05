class GenericPopup
  constructor: (@id, data, @modal=false)->
    if !window.popupStack
      window.popupStack = []
    
    @inside = false
    if window.popupStack.length == 0
      $('body').css('overflow', 'hidden');
      $('body').prepend('<div id="popupOverlay"></div>')
    el = $(@id+'-template')
    if el[0] == undefined
      throw "Wrong popup template"
      return
    @el = $(el.html())
    
    $('body').prepend(@el)
    ko.applyBindings({data: data, close:@close}, @el[0])
    ko.processAllDeferredBindingUpdates()
    if @modal
      return

    window.popupStack.push @
    do @rebind
    
  rebind: =>
    $(window).unbind 'keyup'
    $(window).keyup (e) =>
      if e.keyCode == 27
        console.error @
        @close()

    $(@el.find('table')[0] || @el.find('.wrapContent')).hover =>
      @inside = true
    , =>
      @inside = false
    @el.click =>
      if !@inside
        @close()


  close: =>
    if !@modal
      $(window).unbind 'keyup'
    @el.remove()
    @this = window.popupStack.pop()
    if window.popupStack.length
      window.popupStack[window.popupStack.length-1].rebind()
    else
      $('#popupOverlay').remove()
      $('body').css('overflow', 'auto');
