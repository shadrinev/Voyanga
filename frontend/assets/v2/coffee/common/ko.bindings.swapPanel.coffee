ko.bindingHandlers.swapPanel =
  update: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor()
    $(element).off 'click'
    $(element).on 'click', ->
      newHref = value.to
      window.app.navigate newHref, {'trigger': true}