// Generated by CoffeeScript 1.4.0

ko.rangeObservable = function(from, to) {
  var result, vm;
  vm = {
    from: ko.observable(from),
    to: ko.observable(to)
  };
  result = ko.computed({
    read: function() {
      return {
        from: this.from(),
        to: this.to()
      };
    },
    write: function(value) {
      var parts;
      parts = value.split(';');
      this.from(+parts[0]);
      return this.to(+parts[1]);
    },
    owner: vm
  });
  return result;
};
