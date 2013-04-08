class HelpLayerElement
  constructor: (configOptions,@parent)->
    @obj = $(configOptions.selector)
    @selector = configOptions.selector
    @posTop = ko.observable(0)
    @posLeft = ko.observable(0)
    @dx = 0
    if configOptions.dx
      @dx = configOptions.dx
    @dy = 0
    if configOptions.dy
      @dy = configOptions.dy
    if configOptions.image
      @jElem = $('<div class="imageLayer"><img src="'+configOptions.image+'"/></div>')
    else
      @jElem = $('<div class="'+configOptions.class+'"></div>')

    if configOptions.close
      @jElem.click(
        =>
          @parent.close()
      )
    @parent.mainLayer.append(@jElem)
    @posTop.subscribe (newVal)=>
      ddy = @dy
      if typeof(@dy) == 'string'
        hh = $(@selector).height()
        ddy = ddy.replace('h',hh.toString())
        ddy = eval(ddy)
      @jElem.css({'top':(newVal+ddy) + 'px'})
    @posLeft.subscribe (newVal)=>
      ddx = @dx
      if typeof(@dx) == 'string'
        if @dx == 'center'
          @jElem.css({'left':' 50%'})
          return
        ww = $(@selector).width()
        ddx = ddx.replace('w',ww.toString())
        ddx = eval(ddx)
      @jElem.css({'left':(newVal+ddx) + 'px'})
    @refresh()

  refresh: =>
    if $(@selector).length > 0
      offset = $(@selector).offset()
      @posTop(offset.top)
      @posLeft(offset.left)


class HelpLayer
  constructor: ->
    @pageName = ko.observable 'main'
    @pageName.subscribe (newVal)=>
      @inited = false
      if $('#helpLayer').length > 0
        $('#helpLayer').remove()
      @layerElements([])
    @pageElements = {
      #'main':[{selector:'.tdCity',class:'hint-input',dx:40,dy:21},],
      'tours':[
        {selector:'.left-content .my-trip-list',class:'hint-tours-elements',dx:'w',dy:'h/2'},
        {selector:'.left-content .finish-result:eq(0)',class:'hint-tours-selected-price-info',dx:'w - 185'},
        {selector:'.left-content .finish-result.voyasha',class:'hint-tours-voyasha',dx:'w'},
        {selector:'.panelTable',class:'hint-tours-route-edit',dy:'h',dx:'w/2'},
        {selector:'.filter-block .innerFilter',class:'hint-tours-filters',dx:0,dy:45},
        {selector:'.left-content .finish-result.voyasha',class:'hint-tours-close-button',dx:'center',dy:'h + 13',close:true},
        {selector:'.panelTable',class:'hint-tours-header',dy:-30,dx:'center'},

      ]
    }
    @layerElements = ko.observableArray([])
    @inited = false
    #@mainLayer = $('#helpLayer')

  tryShow: =>
    key = 'helpLayer'+@pageName()
    if @pageElements[@pageName()]
      val = $.cookie(key)
      if !val
        $.cookie(key, true)
        @show()

  zindex: =>
    if !window.POPUP_NEXT_ZINDEX
      window.POPUP_NEXT_ZINDEX = 2000
    return window.POPUP_NEXT_ZINDEX++

  init: =>
    if !@inited
      @mainLayer = $('<div id="helpLayer" style="z-index:' + @zindex() + '"><div class="grayLayer"></div></div>')
      fullHeight = $('html')[0].scrollHeight || $('body')[0].scrollHeight
      @mainLayer.css('height',fullHeight+'px')
      $('body').prepend(@mainLayer)
      for opts in @pageElements[@pageName()]
        @layerElements.push new HelpLayerElement(opts,@)
      closeDiv = $('<div class="hint-close"></div>')
      @mainLayer.append(closeDiv)
      closeDiv.click(
        =>
          @close()
      )
      @inited = true
    else
      @refresh()

  show: =>
    $(window).unbind 'keyup'
    $(window).keyup (e) =>
      if e.keyCode == 27
        @close()
    @init()
    @mainLayer.show()
    @mainLayer.animate(
      {
        opacity: 0.98
      },
      300,
      ->
        voyanga_debug("opacitied")
    )

  close: =>
    $(window).unbind 'keyup'
    @mainLayer.animate(
      {
        opacity: 0,
      },
      300,
      =>
        @mainLayer.hide()
    )

  refresh: =>
    for elem in @layerElements()
      elem.refresh()

