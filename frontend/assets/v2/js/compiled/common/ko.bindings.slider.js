// Generated by CoffeeScript 1.4.0

ko.bindingHandlers.slider = {
  init: function(element, valueAccessor) {
    var value;
    value = ko.utils.unwrapObservable(valueAccessor());
    return $(element).selectSlider({});
  },
  update: function(element, valueAccessor) {}
};
