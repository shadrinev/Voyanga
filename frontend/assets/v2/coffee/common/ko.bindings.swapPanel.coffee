ko.bindingHandlers.swapPanel =
  update: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor()
    $(element).on 'click', ()->
      newHref = window.location.protocol + '//' + window.location.hostname + '#' + value.to
      window.location.href = newHref