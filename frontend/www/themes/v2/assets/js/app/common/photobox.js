var PhotoBox,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

PhotoBox = (function() {

  function PhotoBox(photos) {
    var _this = this;
    this.photos = photos;
    this._load = __bind(this._load, this);

    this.prev = __bind(this.prev, this);

    this.next = __bind(this.next, this);

    this.photoLoad = __bind(this.photoLoad, this);

    if (photos.length === 0) {
      return;
    }
    this.activeIndex = ko.observable(0);
    this.length0 = photos.length - 1;
    this.activePhoto = ko.observable(this.photos[this.activeIndex()]['largeUrl']);
    this.busy = false;
    $('body').prepend('<div id="popupOverlayPhoto"></div>');
    $('body').prepend($('#photo-popup-template').html());
    ko.applyBindings(this, $('#body-popup-Photo')[0]);
    ko.processAllDeferredBindingUpdates();
    resizeLoad();
    resizePhotoWin();
    $(window).keyup(function(e) {
      if (e.keyCode === 27) {
        return _this.close();
      }
    });
  }

  PhotoBox.prototype.close = function() {
    $(window).unbind('keyup');
    $('#body-popup-Photo').remove();
    return $('#popupOverlayPhoto').remove();
  };

  PhotoBox.prototype.photoLoad = function(context, event) {
    var el;
    console.log("PHOTOLOAD");
    el = $(event.currentTarget);
    el.show();
    if (el.width() > 850) {
      el.css('width', '850px');
    } else {
      el.css('width', 'auto');
    }
    $('#hotel-img-load').hide();
    el.animate({
      opacity: 1
    }, 200);
    return this.busy = false;
  };

  PhotoBox.prototype.next = function(context, event) {
    if (this.busy) {
      return;
    }
    if (this.activeIndex() >= this.length0) {
      return;
    }
    this.activeIndex(this.activeIndex() + 1);
    return this._load();
  };

  PhotoBox.prototype.prev = function(context, event) {
    if (this.busy) {
      return;
    }
    if (this.activeIndex() <= 0) {
      return;
    }
    this.activeIndex(this.activeIndex() - 1);
    return this._load();
  };

  PhotoBox.prototype._load = function(var1, var2) {
    var _this = this;
    console.log(var1, var2, this);
    $('#photoBox').find('img').animate({
      opacity: 0
    }, 100, function() {
      return _this.activePhoto(_this.photos[_this.activeIndex()]['largeUrl']);
    });
    return $('#hotel-img-load').show();
  };

  return PhotoBox;

})();
