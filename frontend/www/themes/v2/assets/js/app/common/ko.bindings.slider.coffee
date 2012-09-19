# FIXME most likely leaks
ko.bindingHandlers.slider =
  init: (element, valueAccessor) ->
    console.log "SLIDER"
    value = ko.utils.unwrapObservable valueAccessor()
#    $(element).addClass('selectSlider')
    $(element).selectSlider({})

  update: (element, valueAccessor) ->
    #value = ko.utils.unwrapObservable valueAccessor()
    #console.log @slider, value.from, value.to, "!!!!!!!!"
    #@slider("value", value.from, value.to)
  