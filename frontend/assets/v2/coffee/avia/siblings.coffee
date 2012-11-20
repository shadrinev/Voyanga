class Sibling
  constructor: (@graphHeight, @parent, @price, @delta, date, isActive=false)->
    @rawDate = date
    @date = date.format('D')
    @dow = date.format('dd')
    @month = date.format('MMM')
    @data = []
    @nodata = false
    @isActive = ko.observable isActive

    @scaledHeight = ko.computed =>
      spacing = 30
      scale = @absDelta /(@graphHeight() - spacing)
      @height/scale + spacing - 10

  columnValue: ->
    return @price
    
  background: ->
    if @nodata
       @graphHeight()
    "center " + @scaledHeight() + "px"

class Siblings
  constructor: (siblings, @roundTrip, todayDate, rtTodayDate)->
    @data = []
    @graphHeight = ko.observable 50
    @populate @data, siblings, todayDate, rtTodayDate
    @active = ko.observable @data[3]
    @selection = ko.observable {price: 0}
    
  # click handler
  select: (sibling) =>
    if sibling.nodata
      return
    if sibling.data.length
      @active sibling
    else
      @selection sibling
    for entry in sibling.parent
      entry.isActive false
    sibling.isActive true

  handleSearch: (date, rtDate=false)=>
    app = window.app
    panel = app.fakoPanel()
    panel.sp.date date.toDate()
    if rtDate!=false
      panel.sp.rtDate rtDate.toDate()

    app.navigate panel.sp.getHash(), {trigger: true}

  search: =>
    if @roundTrip
      @handleSearch @active().rawDate, @selection().rawDate
      return
    @handleSearch @selection().rawDate

  populate: (root, siblings, todayDate, rtTodayDate) =>
    # middle segment price
    todayPrice = siblings[3].price
    for sib, index in siblings
      siblingPrice = sib.price
      date = todayDate.clone().subtract('days', 3-index)
      showMonth = false
      if index == 0
        showMonth = true
        prevMonth = date.month()
      if prevMonth != date.month()
        showMonth = true
        prevMonth = date.month()
        
      if index==3
        isActive = true
      else
        isActive = false
      newsib = new Sibling(@graphHeight, root, siblingPrice, siblingPrice - todayPrice, date, isActive)

      if sib.price==false
        newsib.nodata = true
      newsib.showMonth = showMonth
      root.push newsib
      if sib.siblings.length
        @populate newsib.data, sib.siblings, rtTodayDate
    minPrice = _.min root, (item)-> if item.price==false then todayPrice else item.price 
    maxPrice = _.max root, (item)-> if item.price==false then todayPrice else item.price
    if minPrice.price == false
      minPrice = {price: todayPrice}
    absDelta = maxPrice.price - minPrice.price

    for item in root
      item.height = (maxPrice.price - item.price)
      item.absDelta = absDelta
      