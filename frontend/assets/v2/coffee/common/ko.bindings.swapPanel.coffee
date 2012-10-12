ko.bindingHandlers.swapPanel =
  update: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor()
    $(element).on 'click', ()->
      newHref = value.to
      window.app.navigate newHref, {'trigger': true}