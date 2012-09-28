var API, ToursAPI,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

API = (function() {

  function API() {
    this.call = __bind(this.call, this);
    this.endpoint = 'http://api.voyanga.com/v1/';
  }

  API.prototype.call = function(url, cb) {
    $('#loadWrapBg').show();
    return $.ajax({
      url: "" + this.endpoint + url,
      dataType: 'jsonp',
      success: function(data) {
        cb(data);
        return $('#loadWrapBg').hide();
      }
    });
  };

  return API;

})();

ToursAPI = (function(_super) {

  __extends(ToursAPI, _super);

  function ToursAPI() {
    this.search = __bind(this.search, this);
    return ToursAPI.__super__.constructor.apply(this, arguments);
  }

  ToursAPI.prototype.search = function(cb) {
    return this.call("tour/search?start=BCN&destinations%5B0%5D%5Bcity%5D=MOW&destinations%5B0%5D%5BdateFrom%5D=01.10.2012&destinations%5B0%5D%5BdateTo%5D=10.10.2012&rooms%5B0%5D%5Badt%5D=1&rooms%5B0%5D%5Bchd%5D=0&rooms%5B0%5D%5BchdAge%5D=0&rooms%5B0%5D%5Bcots%5D=0", function(data) {
      return cb(data);
    });
  };

  return ToursAPI;

})(API);
