class landBestPriceBack
  constructor: (data,@parent) ->
    @price = parseInt(data.price)
    @showPrice = ko.computed =>
      return (@price - @parent.showPrice())
    @showPriceText = ko.computed =>
      return Utils.formatPrice(@showPrice())
    @showWidth = ko.computed =>
      if @parent.parent.maxPrice()
        #console.log('sp',@showPrice(),'pmBP',@parent.minBestPrice().showPrice(),'pp',@parent.minBestPrice())
        return Math.ceil( (@showPrice() / @parent.parent.maxPrice())*100 )
      else
        console.log('no maxPrice')
    @backDate = moment(data.dateBack)
    @selected = ko.observable(false)

  selectThis: =>
    @parent.setActiveBack(@backDate.format('YYYY-MM-DD'))
    @parent.selectThis()

class landBestPrice
  constructor: (data,@parent) ->
    @minPrice = ko.observable(parseInt(data.price)+5)
    @date = moment(data.date)
    @_results = {}
    @maxPrice = ko.observable(0)
    @showPrice = ko.computed =>
      return Math.ceil(@minPrice() / 2)
    @showPriceText = ko.computed =>
      return Utils.formatPrice(@showPrice())
    @showWidth = ko.computed =>
      if @parent.maxPrice()
        #console.log('sp',@showPrice(),'pmBP',@parent.minBestPrice().showPrice(),'pp',@parent.minBestPrice())
        return Math.ceil( (@showPrice() / @parent.maxPrice() )*100 )
      else
        console.log('no maxPrice')
    @results = ko.computed =>
      ret = []
      if @parent.datesArr()
        for obj in @parent.datesArr()
          ret.push {date:obj.date,landBP:@_results[obj.date]}
      return ret

    @selected = ko.observable(false)
    @selBack = ko.observable(null)
    @active = ko.observable(null)
    @addBack(data)

  addBack: (data)=>
    back = new landBestPriceBack(data,@)
    if back.price < @minPrice()
      @minPrice(back.price)
      if @selBack()
        @selBack().selected(false)
      back.selected(true)
      @selBack(back)
      @active(back)
    if(back.showPrice() > @maxPrice())
      @maxPrice(back.showPrice())
    @_results[data.dateBack] = back

  setActiveBack: (date)=>
    @active().selected(false)
    @active(@_results[date])
    @active().selected(true)

  selectThis: =>
    @parent.setActive(@date.format('YYYY-MM-DD'))
    setDepartureDate(@date.format('YYYY-MM-DD'))
    setBackDate(@active().backDate.format('YYYY-MM-DD'))


class landBestPriceSet
  constructor: (allData) ->
    @_results = {}
    @dates = {}
    @datesArr = ko.observableArray([])
    #console.log(allData)
    @minBestPrice = ko.observable(null)
    @maxPrice = ko.observable(0)
    @active = ko.observable(null)
    for key,data of allData
      #console.log(key,data)
      if !@dates[data.date]
        @dates[data.date] = true
      if !@dates[data.dateBack]
        @dates[data.dateBack] = true
      if @_results[data.date]
        @_results[data.date].addBack(data)
      else
        @_results[data.date] = new landBestPrice(data,@)
      if(!@minBestPrice() || @_results[data.date].minPrice() < @minBestPrice().minPrice())
        @minBestPrice(@_results[data.date])
        console.log('set new minBestPrice',@minBestPrice())
      if( @_results[data.date].showPrice() > @maxPrice() )
        @maxPrice(@_results[data.date].showPrice())
      if( @_results[data.date].maxPrice() > @maxPrice() )
        @maxPrice(@_results[data.date].maxPrice())


    for dataKey,empty of @dates
      @datesArr.push {date:dataKey,landBP:@_results[dataKey],monthName:'',monthChanged: false,dateText:'',today:false}
    @datesArr.sort(
      (objDateLeft,objDateRight)->
        l = moment(objDateLeft.date).unix()
        r = moment(objDateRight.date).unix()
        if l < r
          return -1
        else if r < l
          return 1
        return 0
    )
    firstElem = true
    today = moment().format("YYYY-MM-DD")
    for dataObj in @datesArr()
      mom = moment(dataObj.date)
      if firstElem
        monthChanged = false
        prevMonth = mom.month()
        monthName = ACC_MONTHS[mom.month()]
      else
        if mom.month() != prevMonth
          monthName = ACC_MONTHS[mom.month()]
          monthChanged = true
        else
          monthName = ''
          monthChanged = false

      dataObj.monthName = monthName
      dataObj.monthChanged = monthChanged
      dataObj.dateText = SHORT_WEEKDAYS[( (mom.day()+6) % 7)]+'<br><span>'+mom.format('DD')+'</span>'
      dataObj.today = dataObj.date == today
      console.log('date',dataObj.date, today,dataObj.date == today)
      if firstElem
        firstElem = false
      prevMonth = mom.month()
      #@datesArr.push {date:dataKey,landBP:@_results[dataKey]}
    console.log('dates',@datesArr())

    @active(@minBestPrice())
    @active().selected(true)
    @selectedPrice = ko.computed =>
      return Utils.formatPrice(@active().active().price)

  setActive: (date)=>
    @active().selected(false)
    @active(@_results[date])
    @active().selected(true)

