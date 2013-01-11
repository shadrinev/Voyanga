ko.bindingHandlers.swapPanel =
  update: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor()
    $(element).off 'click'
    $(element).on 'click', ->
      newHref = value.to
      console.log "switching to " + newHref
      window.app.navigate newHref, {'trigger': true}