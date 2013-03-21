class API
  constructor: ->
    @endpoint = window.apiEndPoint
    @loader = window.VisualLoaderInstance
    do @init

  init: =>
    @loaderDescription = "Идет поиск лучших авиабилетов и отелей<br>Это может занять от 5 до 30 секунд"

  call: (url, cb) =>
    $.ajax
      url: "#{@endpoint}#{url}"
      dataType: 'json'
      timeout: 200000
      success: (data)=>
        #sessionStorage.setItem("#{@endpoint}#{url}", JSON.stringify(data))
        @loader.renew(100)
        # unlock DOM
        window.setTimeout ->
          cb(data)
        ,10
      error: (jqXHR, rest...)=>
        @loader.hide()
        if (jqXHR.status > 0)
          throw new Error("Api call failed: Url: #{url}" + " | Status: " + jqXHR.status + " | Status text '" + jqXHR.statusText + "' | " + jqXHR.getAllResponseHeaders().replace("\n", ";") +  " | " + rest.join(" | "))

class ToursAPI extends API
  init: =>
    @loaderDescription = 'Идет поиск лучших авиабилетов и отелей<br>Это может занять от 5 до 30 секунд'    

  search: (url,cb)=>
    @call url, cb

class AviaAPI extends API
  init: =>
    @loaderDescription = 'Идет поиск лучших авиабилетов<br>Это может занять от 5 до 30 секунд'

  search: (url, cb)=>
    @call url, cb


class HotelsAPI extends API
  init: =>
    @loaderDescription = 'Идет поиск лучших отелей<br>Это может занять от 5 до 30 секунд'
  
  search: (url, cb)=>
    @call url,cb
    
class VisualLoader
  constructor: ->
    @percents = ko.observable(0)
    @separator = 90
    @separatedTime = ko.observable(30)
    @timeoutHandler = null
    @glowState = false
    @glowHandler = null
    @tooltips = []
    @tooltips.push 'Мы всегда показываем только финальную цену без скрытых платежей и комиссий'
    @tooltips.push 'При бронировании комплексных поездок (авиабилет плюс гостиница) мы даём скидку до 10%'
    @tooltips.push 'Наш сайт полностью отвечает международным требованиям безопасности платежных систем'
    @tooltips.push 'Воянга любит тебя сильнее чем твоя бабушка'
    @tooltipInd = null
    @tooltipHandler = null
    @description = ko.observable('')
    @description.subscribe (newVal)=>
      $('#loadWrapBg').find('#loadContentWin .text').html(newVal)

    @timeFromStart = 0

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
    $('#loadWrapBg').find('.tips .text').html(@tooltips[@tooltipInd])



  renew: (percent)=>
    @percents percent
    @setPerc(percent)
    if 98 > percent >= 0
      rand = Math.random()
      if(percent < @separator)
        rtime = Math.ceil(rand * (@separatedTime() / 15))
        if( (rand * (@separatedTime() / 15) ) < 1 )
          rand = 1 / (@separatedTime() / 15)

        newPerc = Math.ceil(rand * (@separator / 15) )
        if((percent + newPerc) > @separator)
          newPerc = @separator - percent
        if(newPerc > 3)
          newPerc = newPerc + Math.ceil( (newPerc / 20) * (Math.random() - 0.5) )
      else
        rtime = Math.ceil(rand * (@separatedTime() / 3))
        newPerc = Math.ceil(Math.random() * 2 )
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


  start: (description, loadTime = 30)=>

    @separatedTime(loadTime)
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
    @tooltipStep()


window.VisualLoaderInstance = new VisualLoader
