class Sibling
  constructor: (@graphHeight, @parent, @price, date, isActive=false)->
    @rawDate = date
    @date = date.format('D')
    @dow = date.format('dd')
    @month = date.format('MMM')
    @data = []
    @nodata = false
    @isActive = ko.observable isActive
    @initialActive = isActive
    console.log "************"
    console.log @parent, @parent.price, @price
    if @parent.price
      @price = @price * 2 - @parent.price

    @scaledHeight = ko.computed =>
      spacing = 30
      scale = @absDelta /(@graphHeight() - spacing)
      @height/scale + spacing - 10

  columnValue: ->
    return @price
    
  background: ->
    if @nodata
      return "center " + @graphHeight() + "px"
    "center " + @scaledHeight() + "px"

class Siblings
  constructor: (siblings, @roundTrip, todayDate, rtTodayDate)->
    @data = []
    @graphHeight = ko.observable 50
    @populate @, siblings, todayDate, rtTodayDate
    @active = ko.observable @data[3]
    @selection = ko.observable false
    
  # click handler
  select: (sibling) =>
    if sibling.data.length
      @active sibling
      for sib in sibling.data
        if sib.isActive()
          @selection sib
          break
    else
      @selection sibling
    for entry in sibling.parent.data
      entry.isActive false
    sibling.isActive true

  showControls: =>
    if !@selection()
      return false
    if @active().initialActive && @selection().initialActive
      return false
    return true

  showPrice: =>
    if !@selection()
      return false
    if @active().nodata || @selection().nodata
      return false
    return true
    
  priceDisplay: =>
    if !@showPrice()
      return ''
  
    if @roundTrip
      @active().price + @selection().price
    else
     @selection().price

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
    if todayPrice == false
      # FIXME FIXME FIXME
      todayPrice = 1
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
      newsib = new Sibling(@graphHeight, root, siblingPrice,  date, isActive)

      if sib.price==false
        newsib.nodata = true
      newsib.showMonth = showMonth
      root.data.push newsib
      if sib.siblings.length
        @populate newsib, sib.siblings, rtTodayDate
    minPrice = _.min root.data, (item)-> if item.price==false then todayPrice else item.price 
    maxPrice = _.max root.data, (item)-> if item.price==false then todayPrice else item.price
    console.log maxPrice, root.data, minPrice
    if maxPrice.price == false
      maxPrice = {price: todayPrice}
    if minPrice.price == false
      minPrice = {price: todayPrice}
    absDelta = maxPrice.price - minPrice.price

    for item in root.data
      item.height = (maxPrice.price - item.price)
      item.absDelta = absDelta
      