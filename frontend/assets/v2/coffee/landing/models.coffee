class landBestPriceBack
  constructor: (data,@parent) ->
    @price = data.price
    @showPrice = ko.computed =>
      return (@price - @parent.showPrice)
    @backDate = moment(data.backDate)
    @selected = ko.observable(false)

class landBestPrice
  constructor: (data) ->
    @minPrice = ko.observable(data.price)
    @date = moment(data.date)
    @backPrices = []
    @showPrice = ko.computed =>
      return Math.ceil(@minPrice() / 2)
    @selected = ko.observable(false)
    @selBack = ko.observable(null)
    @addBack(data)

  addBack: (data)=>
    back = new landBestPriceBack(data,@)
    if back.price < @minPrice()
      @minPrice(back.price)
      if @selBack()
        @selBack().selected(false)
      back.selected(true)
      @selBack(back)
    @backPrices.push back

class landBestPriceSet
  constructor: (allData) ->
    @_results = {}
    #console.log(allData)
    @minBestPrice = false
    for key,data of allData
      #console.log(key,data)
      if @_results[data.date]
        @_results[data.date].addBack(data)
      else
        @_results[data.date] = new landBestPrice(data)
      if(!@minBestPrice || @_results[data.date].minPrice() < @minBestPrice.minPrice())
        if @minBestPrice
          @minBestPrice.selected(false)
        @minBestPrice = @_results[data.date]
        @minBestPrice.selected(true)


