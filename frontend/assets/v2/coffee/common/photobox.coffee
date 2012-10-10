class PhotoBox
  constructor: (@photos,@title,@stars = 0,initIndex = 0)->
    if photos.length == 0
      return
    @activeIndex = ko.observable initIndex
    @length0 = photos.length - 1
    @activePhoto = ko.observable @photos[@activeIndex()]['largeUrl']
    # Indicates if we are sliding right now, so no one should be able
    # to request other image
    @busy = false
    
    $('body').prepend('<div id="popupOverlayPhoto"></div>')
    $('body').prepend($('#photo-popup-template').html())
    # FIXME most likely memory leaks, tho i dont sure
    ko.applyBindings(@, $('#body-popup-Photo')[0])
    ko.processAllDeferredBindingUpdates()

    resizeLoad()
    resizePhotoWin()

    $(window).keyup (e) =>
      if e.keyCode == 27
        @close()
      else if e.keyCode == 37
        @prev()
      else if e.keyCode == 39
        @next()

  close: ->
    $(window).unbind 'keyup'
    $('#body-popup-Photo').remove()
    $('#popupOverlayPhoto').remove()
    
  # Load handler for popup photos
  photoLoad: (context, event) =>
    console.log "PHOTOLOAD"
    el = $(event.currentTarget)
    el.show()
    if el.width() > 850
      el.css 'width', '850px'
    else
      el.css 'width', 'auto'
    $('#hotel-img-load').hide()

    el.animate {opacity : 1}, 200
    #resizeImg();
    @busy = false

  next: (context, event) =>
    if @busy
      return
    if @activeIndex() >= @length0
      return
    @activeIndex(@activeIndex() + 1)
    
    @_load()
	   
  prev: (context, event) =>
    if @busy
      return
    if @activeIndex() <= 0
      return
    @activeIndex(@activeIndex() - 1)

    @_load()

  # load image with @activeIndex
  _load: (var1, var2)=>
    #console.log var1, var2, @
    $('#photoBox').find('img').animate {opacity : 0}, 100, =>
      @activePhoto(@photos[@activeIndex()]['largeUrl'])
  		$('#hotel-img-load').show()
	   