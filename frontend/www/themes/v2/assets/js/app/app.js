var Application,
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
  __slice = [].slice;

Application = (function(_super) {

  __extends(Application, _super);

  function Application() {
    var _this = this;
    this.activeModule = ko.observable(window.activeModule || 'avia');
    this.panel = ko.observable({});
    this._view = ko.observable('index');
    this._sidebar = ko.observable('dummy');
    this.activeView = ko.computed(function() {
      return _this.activeModule() + '-' + _this._view();
    });
    this.activeSidebar = ko.computed(function() {
      return _this.activeModule() + '-' + _this._sidebar();
    });
    this.viewData = ko.observable({});
    this.sidebarData = ko.observable({});
  }

  Application.prototype.register = function(prefix, module, isDefault) {
    var action, controller, route, _ref,
      _this = this;
    if (isDefault == null) {
      isDefault = false;
    }
    controller = module.controller;
    controller.on("viewChanged", function(view, data) {
      _this.viewData(data);
      return _this._view(view);
    });
    controller.on("sidebarChanged", function(sidebar, data) {
      _this.sidebarData(data);
      return _this._sidebar(sidebar);
    });
    _ref = controller.routes;
    for (route in _ref) {
      action = _ref[route];
      window.voyanga_debug("APP: registreing route", prefix, route, action);
      this.route(prefix + route, prefix, action);
      if (isDefault && route === '') {
        this.route(route, prefix, action);
      }
    }
    if (isDefault) {
      this.panel(module.panel);
    }
    return this.on("route:" + prefix, function() {
      var args;
      args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
      if (prefix !== this.activeModule()) {
        window.voyanga_debug("APP: switching active module to", prefix);
        return this.activeModule(prefix);
      }
    });
  };

  Application.prototype.run = function() {
    return Backbone.history.start();
  };

  Application.prototype.http404 = function() {
    return alert("Not found");
  };

  return Application;

})(Backbone.Router);

$(function() {
  var app, avia;
  window.voyanga_debug = function() {
    var args;
    args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
    return console.log.apply(null, args);
  };
  app = new Application();
  avia = new AviaModule();
  app.register('avia', avia, true);
  app.run();
  ko.applyBindings(app);
  return window.app = app;
});
