class landBestPriceBack
  constructor: (data,@parent) ->
    @price = data.price
    @showPrice = ko.computed =>
      return (@price - @parent.showPrice)
    @backDate = moment(data.backDate)
    @selected = ko.observable(false)

class landBestPrice
  constructor: (data,@parent) ->
    @minPrice = ko.observable(data.price)
    @date = moment(data.date)
    @_results = {}
    @showPrice = ko.computed =>
      return Math.ceil(@minPrice() / 2)
    @showPriceText = ko.computed =>
      return Utils.formatPrice(@showPrice())
    @showWidth = ko.computed =>
      if @parent.minBestPrice
        return Math.ceil( (@showPrice / @parent.minBestPrice.showPrice())*100 )

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
    @minBestPrice = false
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
      if(!@minBestPrice || @_results[data.date].minPrice() < @minBestPrice.minPrice())
        @minBestPrice = @_results[data.date]

    @datesArr = []
    for dataKey,empty of @dates
      @datesArr.push {date:dataKey,landBP:@_results[dataKey]}
    @datesArr = _.sortBy(
      @datesArr,
      (objDate)->
        return moment(objDate.date).unix()
    )
    console.log('dates',@datesArr)

    @active(@minBestPrice)
    @active().selected(true)

  setActive: (date)=>
    @active().selected(false)
    @active(@_results[date])
    @active().selected(true)

