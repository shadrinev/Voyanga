class API
  constructor: ->
    @endpoint = window.apiEndPoint
    @loader = new VisualLoader

  call: (url, cb, showLoad = true, description = 'voyanga') =>
    if showLoad
      #$('#loadWrapBg').show()
      #loaderChange(true)
      if(description == 'voyanga')
        description = 'Идет поиск лучших авиабилетов и отелей<br>Это может занять от 5 до 30 секунд'
      @loader.start(description)

    #  $(document).trigger 'aviaStart'
    #if sessionStorage.getItem("#{@endpoint}#{url}")
    #  cb(JSON.parse(sessionStorage.getItem("#{@endpoint}#{url}")))
    #  return $('#loadWrapBg').hide()
     
    $.ajax
      url: "#{@endpoint}#{url}"
      dataType: 'json'
      timeout: 200000
      success: (data)=>
        #sessionStorage.setItem("#{@endpoint}#{url}", JSON.stringify(data))
        if showLoad
          @loader.renew(100)
        window.setTimeout(
          =>
            cb(data)
            if showLoad
              @loader.hide()
          , 50
        )

          #$('#loadWrapBg').hide()
          #loaderChange(false)
      error: (jqXHR, rest...)->
        if showLoad
          @loader.hide()
          #$('#loadWrapBg').hide()
          #loaderChange(false)
        throw new Error("Api call failed: Url: #{url}" + " | Status: " + jqXHR.status + " | Status text '" + jqXHR.statusText + "' | " + jqXHR.getAllResponseHeaders().replace("\n", ";") +  " | " + rest.join(" | "))
#        cb(false)

class ToursAPI extends API
  search: (url,cb, showLoad = true, description = '')=>
    if(showLoad && !description)
      description = 'Идет поиск лучших авиабилетов и отелей<br>Это может занять от 5 до 30 секунд'
    #@call "tour/search?start=BCN&destinations%5B0%5D%5Bcity%5D=MOW&destinations%5B0%5D%5BdateFrom%5D=10.10.2012&destinations%5B0%5D%5BdateTo%5D=15.10.2012&rooms%5B0%5D%5Badt%5D=1&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0", (data) -> cb(data)
    @call(
      url,
      (data) ->
        cb(data)
      , showLoad,
      description
    )

class AviaAPI extends API
  search: (url, cb, showLoad = true, description = '')=>
    if(showLoad && !description)
      description = 'Идет поиск лучших авиабилетов<br>Это может занять от 5 до 30 секунд'
    #@call "tour/search?start=BCN&destinations%5B0%5D%5Bcity%5D=MOW&destinations%5B0%5D%5BdateFrom%5D=10.10.2012&destinations%5B0%5D%5BdateTo%5D=15.10.2012&rooms%5B0%5D%5Badt%5D=1&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0", (data) -> cb(data)
    @call(
      url,
      (data) ->
        cb(data)
      , showLoad,
      description
    )


class HotelsAPI extends API
  search: (url, cb, showLoad = true, description = '')=>
    if(showLoad && !description)
      description = 'Идет поиск лучших отелей<br>Это может занять от 5 до 30 секунд'
    #@call "tour/search?start=BCN&destinations%5B0%5D%5Bcity%5D=MOW&destinations%5B0%5D%5BdateFrom%5D=10.10.2012&destinations%5B0%5D%5BdateTo%5D=15.10.2012&rooms%5B0%5D%5Badt%5D=1&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0", (data) -> cb(data)
    @call(
      url,
      (data) ->
        cb(data)
      , showLoad,
      description
    )

class VisualLoader
  constructor: ->
    @percents = ko.observable(0)
    @separator = 90
    @separatedTime = 30
    @timeoutHandler = null
    @glowState = false
    @glowHandler = null
    @tooltips = ['aga1','aga2']
    @tooltipInd = null
    @tooltipHandler = null
    @description = ko.observable('')
    @description.subscribe (newVal)=>
      $('#loadWrapBg').find('.text').html(newVal)

    @timeFromStart = 0
    @percents.subscribe (newVal)=>
      console.log('loder changed... NOW: '+newVal + '% time from start: '+ @timeFromStart+'sec')

  show: =>
    $('#loadWrapBg').show()

  hide: =>
    $('#loadWrapBg').hide()
    if(@glowHandler)
      window.clearInterval(@glowHandler)
      @glowHandler = null

    if(@tooltipHandler)
      window.clearInterval(@tooltipHandler)
      @tooltipHandler = null

  setPerc: (perc)=>
    h = Math.ceil( (156 - (perc / 100) * 156 ) )
    $('#loadWrapBg').find('.procent .digit').html(perc)
    $('#loadWrapBg').find('.layer03').height(h)

  glowStep: =>
    if @glowState
      $('#loadWrapBg').find('.procent .symbol').addClass('glowMore')
    else
      $('#loadWrapBg').find('.procent .symbol').removeClass('glowMore')
    @glowState = !@glowState


  tooltipStep: =>
    count = @tooltips.length
    randVal = Math.ceil(Math.random() * count)
    randInd = randVal % count
    if randInd == @tooltipInd
      randInd = (randVal+1) % count
    @tooltipInd = randInd


  renew: (percent)=>
    @percents percent
    @setPerc(percent)
    if 98 > percent >= 0
      rand = Math.random()
      if(percent < @separator)
        rtime = Math.ceil(rand * (@separatedTime / 15))
        newPerc = Math.ceil(rand * (@separator / 15) )
        if((percent + newPerc) > @separator)
          newPerc = @separator - percent
        if(newPerc > 3)
          newPerc = newPerc + Math.ceil( (newPerc / 20) * (Math.random() - 0.5) )
      else
        rtime = Math.ceil(rand * (@separatedTime / 3))
        newPerc = Math.ceil(Math.random() * 2 )
      console.log('time: '+rtime+'sec')
      @timeFromStart +=rtime
      @timeoutHandler = window.setTimeout(
        =>
          if (percent + newPerc) > 100
            newPerc = 98 - percent
          @renew(percent + newPerc)
        , 1000 * rtime
      )
    else if 100 > percent >= 98
      console.log('loadrer more 98')
    else
      if(@timeoutHandler)
        window.clearTimeout(@timeoutHandler)
      if(@glowHandler)
        window.clearInterval(@glowHandler)
        @glowHandler = null

      @timeoutHandler = null


  start: (description)=>

    @description description
    @timeFromStart = 0
    if !@glowHandler
      @glowHandler = window.setInterval(
        =>
          @glowStep()
        , 500
      )
    if !@tooltipHandler
      @tooltipHandler = window.setInterval(
        =>
          @tooltipStep()
        , 10000
      )
    @show()
    @renew 3


