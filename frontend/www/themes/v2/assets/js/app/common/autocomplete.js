
ko.bindingHandlers.autocomplete = {
  init: function(element, params) {
    var options;
    options = params().split(" ");
    $(element).bind("focus", function() {
      return $(element).change();
    });
    return $(element).autocomplete({
      serviceUrl: "http://api.voyanga.com/v1/helper/autocomplete/" + options[0],
      minChars: 2,
      delimiter: /(,|;)\s*/,
      maxHeight: 400,
      width: 300,
      zIndex: 9999,
      deferRequestBy: 50,
      country: "Yes",
      onSelect: function(data, value) {
        console.log(data);
        console.log(value);
        return $(element).val(value);
      }
    });
  },
  update: function(element, params) {}
};
