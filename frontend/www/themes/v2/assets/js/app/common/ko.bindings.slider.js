
ko.bindingHandlers.slider = {
  init: function(element, valueAccessor) {
    var value;
    console.log("SLIDER");
    value = ko.utils.unwrapObservable(valueAccessor());
    return $(element).selectSlider({});
  },
  update: function(element, valueAccessor) {}
};
