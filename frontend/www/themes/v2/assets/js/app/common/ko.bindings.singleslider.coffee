# FIXME most likely leaks
ko.bindingHandlers.singleSlider =
  init: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor().selection
    limits = ko.utils.unwrapObservable valueAccessor().limits
    #limits.from -= 15
    limits.from = 0 if limits.from < 0
    value = limits.to
    dimension = $(element).data('dimension')
    if dimension
      dimension = '&nbsp;'+ dimension

    $(element).val(value)
    $(element).slider
      from: limits.from,
      to: limits.to,
      dimension: dimension,
      skin: 'round_voyanga',
      scale: false,
      limits: false,
      round: 1,
      calculate: (value)->
        # FIXME use date utils here
        strVal = value.toString()
        if strVal.length > 3
          strVal = strVal.substr(0,strVal.length - 3) + '&nbsp;' + strVal.substr(-3)
        strVal
      callback: (newValue) ->
        valueAccessor().selection(newValue)
  update: (element, valueAccessor) ->
    s = $(element).data("jslider")
    # FIXME FIXME FIXME
    setTimeout ->
      s.onresize()
    , 5