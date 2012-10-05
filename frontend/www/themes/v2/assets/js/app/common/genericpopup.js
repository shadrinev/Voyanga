var GenericPopup,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

GenericPopup = (function() {

  function GenericPopup(id, data) {
    var el,
      _this = this;
    this.id = id;
    this.close = __bind(this.close, this);

    $('body').prepend('<div id="popupOverlay"></div>');
    el = $($(this.id + '-template').html());
    $('body').prepend(el);
    if (data['$data']) {
      if (!data['$data']['data']) {
        data['$data'] = {
          data: data['$data']
        };
      }
      data['$data']['close'] = this.close;
      ko.applyBindings(data, el[0]);
    } else {
      ko.applyBindings({
        data: data,
        close: this.close
      }, el[0]);
    }
    ko.processAllDeferredBindingUpdates();
    $(window).keyup(function(e) {
      if (e.keyCode === 27) {
        return _this.close();
      }
    });
    $('#popupOverlay').click(function() {
      return _this.close();
    });
  }

  GenericPopup.prototype.close = function() {
    $(window).unbind('keyup');
    $(this.id).remove();
    return $('#popupOverlay').remove();
  };

  return GenericPopup;

})();
