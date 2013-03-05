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
        cb(data)
        if showLoad
          @loader.hide()
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
  search: (url,cb)=>
    #@call "tour/search?start=BCN&destinations%5B0%5D%5Bcity%5D=MOW&destinations%5B0%5D%5BdateFrom%5D=10.10.2012&destinations%5B0%5D%5BdateTo%5D=15.10.2012&rooms%5B0%5D%5Badt%5D=1&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0", (data) -> cb(data)
    @call url, (data) -> cb(data)

class AviaAPI extends API
  search: (url, cb)=>
    @call url, (data) -> cb(data)

class HotelsAPI extends API
  search: (url, cb, showLoad = true)=>
    @call(
      url,
      (data) ->
        cb(data)
      , showLoad
    )

class VisualLoader
  constructor: ->
    @percents = ko.observable(0)
    @separator = 90
    @separatedTime = 30
    @timeoutHandler = null
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

  setPerc: (perc)=>
    h = Math.ceil( (156 - (perc / 100) * 156 ) )
    $('#loadWrapBg').find('.procent').html(perc + '<span class="simbol"></span>')
    $('#loadWrapBg').find('.layer03').height(h)


  renew: (percent)=>
    @percents percent
    @setPerc(percent)
    if 98 > percent >= 0
      rand = Math.random()
      if(percent < @separator)
        rtime = Math.ceil(rand * (@separatedTime / 3))
        newPerc = Math.ceil(rand * (@separator / 3) )
        if((percent + newPerc) > @separator)
          newPerc = @separator - percent
        if(newPerc > 3)
          newPerc = newPerc + Math.ceil( (newPerc / 10) * (Math.random() - 0.5) )
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

      @timeoutHandler = null


  start: (description)=>
    @description description
    @timeFromStart = 0
    @show()
    @renew 0


