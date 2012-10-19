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

    $(element).val(value.from+';'+value.to)
    $(element).jslider
      from: limits.from,
      to: limits.to,
      dimension: '&nbsp;Р',
      skin: 'round_voyanga',
      scale: false,
      limits: false,
      minInterval: 60,
      calculate: (value)->
        # FIXME use date utils here
        strVal = value.toString()
        if strVal.length > 3
          strVal = strVal.substr(0,strVal.length - 3) + '&nbsp;' + strVal.substr(-3)
        strVal
      callback: (newValue) ->
        valueAccessor().selection(newValue)
    valueAccessor().element = $(element)

  update: (element, valueAccessor) ->
    s = $(element).data("jslider")
    # FIXME FIXME FIXME
    setTimeout ->
      s.onresize()
    , 5