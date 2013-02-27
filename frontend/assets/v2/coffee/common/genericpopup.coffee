class GenericPopup
  constructor: (@id, data, @modal=false)->
    if !window.popupStack
      window.popupStack = []
    
    @inside = false
    if window.popupStack.length == 0
      $('body').css('overflow', 'hidden');
    @overlay = $('<div id="popupOverlay" style="z-index:' + @zindex() + '"></div>')
    $('body').prepend(@overlay)
    el = $(@id+'-template')
    if el[0] == undefined
      throw "Wrong popup template"
      return
    @el = $(el.html())
    
    $('body').prepend(@el)
    @el.css('z-index', @zindex())
    ko.applyBindings({data: data, close:@close}, @el[0])
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
    @overlay.remove()
    if window.popupStack.length
      window.popupStack[window.popupStack.length-1].rebind()
    else
      $('body').css('overflow', 'auto');


  zindex: =>
    if !window.POPUP_NEXT_ZINDEX
      window.POPUP_NEXT_ZINDEX = 2000
    return window.POPUP_NEXT_ZINDEX++