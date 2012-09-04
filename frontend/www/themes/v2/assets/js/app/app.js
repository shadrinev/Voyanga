var Application,
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

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

  Application.prototype.register = function(prefix, controller, isDefault) {
    var action, route, _ref, _results,
      _this = this;
    if (isDefault == null) {
      isDefault = false;
    }
    controller.on("viewChanged", function(view, data) {
      _this.viewData(data);
      return _this._view(view);
    });
    controller.on("sidebarChanged", function(sidebar, data) {
      _this.sidebarData(data);
      return _this._sidebar(sidebar);
    });
    controller.on("panelChanged", function(panel) {
      return _this.panel(panel);
    });
    _ref = controller.routes;
    _results = [];
    for (route in _ref) {
      action = _ref[route];
      this.route(prefix + route, prefix, action);
      if (isDefault && route === '') {
        _results.push(this.route(route, prefix, action));
      } else {
        _results.push(void 0);
      }
    }
    return _results;
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
  var app;
  window.VOYANGA_DEBUG = true;
  app = new Application();
  app.register('avia', new AviaController(), true);
  app.run();
  ko.applyBindings(app);
  return window.app = app;
});
