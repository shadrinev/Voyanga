var Application,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

Application = (function() {

  function Application() {
    this.navigate = __bind(this.navigate, this);

    var _this = this;
    hasher.initialized.add(this.navigate);
    hasher.changed.add(this.navigate);
    crossroads.bypassed.add(this.http404);
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
    controller.viewChanged.add(function(view, data) {
      _this.viewData(data);
      return _this._view(view);
    });
    controller.sidebarChanged.add(function(sidebar, data) {
      _this.sidebarData(data);
      return _this._sidebar(sidebar);
    });
    controller.panelChanged.add(function(panel) {
      return _this.panel(panel);
    });
    _ref = controller.routes;
    _results = [];
    for (route in _ref) {
      action = _ref[route];
      crossroads.addRoute(prefix + route).matched.add(action);
      if (isDefault && route === '') {
        _results.push(crossroads.addRoute(route).matched.add(action));
      } else {
        _results.push(void 0);
      }
    }
    return _results;
  };

  Application.prototype.run = function() {
    return hasher.init();
  };

  Application.prototype.navigate = function(newUrl, oldUrl) {
    return crossroads.parse(newUrl);
  };

  Application.prototype.http404 = function() {
    return alert("Not found");
  };

  return Application;

})();

$(function() {
  var app;
  app = new Application();
  app.register('avia', new AviaController(), true);
  app.run();
  return ko.applyBindings(app);
});
