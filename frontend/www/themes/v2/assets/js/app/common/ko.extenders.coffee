ko.extenders.integerOnly = (target, config)->
  ko.computed
    read: target,
    write: (newValue) ->
      current = target()
      valueToWrite = parseInt(newValue)
      if isNaN(valueToWrite) || valueToWrite < 0
        valueToWrite = 0
      # FIXME HARDCODE
      if config == "adult" && valueToWrite < 1
        valueToWrite = 1
      if config == "infant" && valueToWrite > 4
        valueToWrite = 4

      if valueToWrite != current
        target(valueToWrite)
      if newValue != current
        target.notifySubscribers(valueToWrite)

