# FIXME most likely leaks
ko.bindingHandlers.timeSlider =
  init: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor().selection
    limits = ko.utils.unwrapObservable valueAccessor().limits
    limits.from -= 15
    limits.from = 0 if limits.from < 0
    limits.to += 15
    limits.to = 1440 if limits.to > 1440
    value.from = limits.from unless Utils.inRange(value.from, limits)
    value.to = limits.to unless Utils.inRange(value.to, limits)

    $(element).val(value.from+';'+value.to)
    $(element).jslider
      from: limits.from,
      to: limits.to,
      step: 15,
      dimension: '',
      skin: 'round_voyanga',
      scale: false,
      limits: false,
      minInterval: 60,
      calculate: (value)->
        # FIXME use date utils here
        hours = Math.floor( value / 60 );
        mins = ( value - hours*60 );
        hours = "0"+hours if hours < 10
        mins = "00" if mins == 0
        hours + ':' + mins
      callback: (newValue) ->
        valueAccessor().selection(newValue)
    valueAccessor().element = $(element)

  update: (element, valueAccessor) ->
    s = $(element).data("jslider")
    # FIXME FIXME FIXME
    setTimeout ->
      s.onresize()
    , 5