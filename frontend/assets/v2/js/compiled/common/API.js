// Generated by CoffeeScript 1.3.3
var API, AviaAPI, HotelsAPI, ToursAPI,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

API = (function() {

  function API() {
    this.call = __bind(this.call, this);
    this.endpoint = 'http://api.voyanga.com/v1/';
  }

  API.prototype.call = function(url, cb) {
    var _this = this;
    $('#loadWrapBg').show();
    if (sessionStorage.getItem("" + this.endpoint + url)) {
      cb(JSON.parse(sessionStorage.getItem("" + this.endpoint + url)));
      return $('#loadWrapBg').hide();
    }
    return $.ajax({
      url: "" + this.endpoint + url,
      dataType: 'jsonp',
      timeout: 60000,
      success: function(data) {
        sessionStorage.setItem("" + _this.endpoint + url, JSON.stringify(data));
        cb(data);
        return $('#loadWrapBg').hide();
      },
      error: function() {
        console.log("ERROR");
        $('#loadWrapBg').hide();
        return cb(false);
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

  ToursAPI.prototype.search = function(url, cb) {
    return this.call(url, function(data) {
      return cb(data);
    });
  };

  return ToursAPI;

})(API);

AviaAPI = (function(_super) {

  __extends(AviaAPI, _super);

  function AviaAPI() {
    this.search = __bind(this.search, this);
    return AviaAPI.__super__.constructor.apply(this, arguments);
  }

  AviaAPI.prototype.search = function(url, cb) {
    return this.call(url, function(data) {
      return cb(data);
    });
  };

  return AviaAPI;

})(API);

HotelsAPI = (function(_super) {

  __extends(HotelsAPI, _super);

  function HotelsAPI() {
    this.search = __bind(this.search, this);
    return HotelsAPI.__super__.constructor.apply(this, arguments);
  }

  HotelsAPI.prototype.search = function(url, cb) {
    return this.call(url, function(data) {
      return cb(data);
    });
  };

  return HotelsAPI;

})(API);
