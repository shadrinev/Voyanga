class PhotoBox extends GenericPopup
  constructor: (@photos,@title,@stars = 0,initIndex = 0)->
    if photos.length == 0
      return
    
    @activeIndex = ko.observable initIndex
    @length0 = photos.length - 1
    @activePhoto = ko.observable @photos[@activeIndex()]['largeUrl']
    @activeDesc = ko.observable @photos[@activeIndex()]['description']
    # Indicates if we are sliding right now, so no one should be able
    # to request other image
    @busy = false
    super('#photo-popup', @, false)

#    $('body').prepend('<div id="popupOverlayPhoto"></div>')

    resizeLoad()
    #resizePhotoWin()
  rebind: =>
    super
    $(window).keyup (e) =>
      if e.keyCode == 37
        @prev()
      else if e.keyCode == 39
        @next()

    
  # Load handler for popup photos
  photoLoad: (context, event) =>
    el = $(event.currentTarget)
    el.show()
    console.log(el)
    if el.width() > 850
      el.css 'width', '850px'
    else
      el.css 'width', 'auto'
    $('#hotel-img-load').hide()
    #resizePhotoWinHandler()

    el.animate({opacity : 1},300, ->
      console.log("opacitied")
    )
    #resizeImg();
    @busy = false

  next: (context, event) =>
    if @busy
      return
    if @activeIndex() >= @length0
      return
    @activeIndex(@activeIndex() + 1)
    #@activePhoto(@photos[@activeIndex()]['largeUrl'])
    @_load()
	   
  prev: (context, event) =>
    if @busy
      return
    if @activeIndex() <= 0
      return
    @activeIndex(@activeIndex() - 1)
    #@activePhoto(@photos[@activeIndex()]['largeUrl'])
    @_load()

  # load image with @activeIndex
  _load: (var1, var2)=>
    $('#body-popup-Photo').find('table img').animate {opacity : 0}, 300, =>
      @activePhoto(@photos[@activeIndex()]['largeUrl'])
      @activeDesc(@photos[@activeIndex()]['description'])
  		$('#hotel-img-load').show()
	   