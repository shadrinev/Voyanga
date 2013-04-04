# FIXME most likely leaks
ko.bindingHandlers.priceSlider =
  init: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor().selection
    limits = ko.utils.unwrapObservable valueAccessor().limits
    #limits.from -= 15
    limits.from = 0 if limits.from < 0
    #limits.to += 15
    console.log limits.to
    #limits.to = 1440 if limits.to > 1440
    value.from = limits.from unless Utils.inRange(value.from, limits)
    value.to = limits.to unless Utils.inRange(value.to, limits)
    @limits = limits
    @ampl = @limits.to - @limits.from
    if @ampl > 80000
      @xPoints = [0, 0.25, 0.4, 0.5,0.75,1]
      @yPoints = [0,0.002,0.04,0.05, 0.4,1]
      @xK = []
      @xB = []

      for i in [1..@xPoints.length]
        x1 = @xPoints[(i-1)]
        x2 = @xPoints[i]
        y1 = 0 + @ampl*@yPoints[(i-1)]
        y2 = 0 + @ampl*@yPoints[i]
        k= ((y2-y1) / (x2-x1))
        b= ((x2*y1-x1*y2) / (x2-x1))
        @xK.push k
        @xB.push b
      @getY = (value)=>
        x = (value - @limits.from) / @ampl
        for i in [0..(@xPoints.length-1)]
          if @xPoints[i] <= x <= @xPoints[(i+1)]
            return (@limits.from + x*@xK[i] + @xB[i])
    else
      @getY = (value)=>
        return value

    $(element).val(value.from+';'+value.to)
    $(element).jslider
      from: limits.from,
      to: limits.to,
      dimension: '&nbsp;Р',
      skin: 'round_voyanga',
      scale: false,
      limits: false,
      minInterval: 60,
      calculate: (value)=>

        y = Math.ceil(@getY(value))
        strVal = y.toString()
        if strVal.length > 3
          strVal = strVal.substr(0,strVal.length - 3) + '&nbsp;' + strVal.substr(-3)
        strVal
      callback: (newValue) =>
        parts = newValue.split(';')
        vals = []
        vals.push(Math.ceil(@getY(parts[0])))
        vals.push(Math.ceil(@getY(parts[1])))
        valueAccessor().selection(vals.join(';'))
    valueAccessor().element = $(element)

  update: (element, valueAccessor) ->
    s = $(element).data("jslider")
    # FIXME FIXME FIXME
    setTimeout ->
      s.onresize()
    , 5