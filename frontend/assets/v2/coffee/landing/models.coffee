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
        return Math.ceil( (@showPrice() / (@parent.parent.maxPrice() / 2))*100 )
      else
        console.log('no minPrice',@parent.minBestPrice())
    @backDate = moment(data.backDate)
    @selected = ko.observable(false)

class landBestPrice
  constructor: (data,@parent) ->
    @minPrice = ko.observable(parseInt(data.price))
    @date = moment(data.date)
    @_results = {}
    @showPrice = ko.computed =>
      return Math.ceil(@minPrice() / 2)
    @showPriceText = ko.computed =>
      return Utils.formatPrice(@showPrice())
    @showWidth = ko.computed =>
      if @parent.maxPrice()
        #console.log('sp',@showPrice(),'pmBP',@parent.minBestPrice().showPrice(),'pp',@parent.minBestPrice())
        return Math.ceil( (@showPrice() / (@parent.maxPrice() / 2))*100 )
      else
        console.log('no minPrice',@parent.minBestPrice())
    @results = ko.computed =>
      ret = []
      for obj in @parent.datesArr
        ret.push {data:obj.data,landBP:@_results[obj.data]}
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
    @_results[data.backDate] = back

  setActiveBack: (date)=>
    @active().selected(false)
    @active(@_results[date])
    @active().selected(true)

class landBestPriceSet
  constructor: (allData) ->
    @_results = {}
    @dates = {}
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
      if( parseInt(data.price) > @maxPrice() )
        @maxPrice(parseInt(data.price))

    @datesArr = []
    for dataKey,empty of @dates
      @datesArr.push {date:dataKey,landBP:@_results[dataKey]}
    @datesArr = _.sortBy(
      @datesArr,
      (objDate)->
        return moment(objDate.date).unix()
    )
    console.log('dates',@datesArr)

    @active(@minBestPrice())
    @active().selected(true)

  setActive: (date)=>
    @active().selected(false)
    @active(@_results[date])
    @active().selected(true)

