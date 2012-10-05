ko.rangeObservable = (from, to) ->
  vm = 
    from: ko.observable from
    to:  ko.observable to
  result = ko.computed 
    read: ->
      {from: @from(), to:@to()}
    write: (value) ->
      parts = value.split(';')
      @from +parts[0]
      @to +parts[1]
    owner: vm

  return result