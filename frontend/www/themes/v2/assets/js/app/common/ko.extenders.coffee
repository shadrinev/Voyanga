ko.extenders.integerOnly = (target)->
  ko.computed
    read: target,
    write: (newValue) ->
      current = target()
      valueToWrite = parseInt(newValue)
      if isNaN(valueToWrite) || valueToWrite < 0
        valueToWrite = 0
      if valueToWrite != current
        target(valueToWrite)
      else if newValue != current
        target.notifySubscribers(valueToWrite)

