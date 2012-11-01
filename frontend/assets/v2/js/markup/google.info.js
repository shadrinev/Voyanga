/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 01.11.12
 * Time: 17:44
 * To change this template use File | Settings | File Templates.
 */
function googleInfoDiv() {
    this.extend(googleInfoDiv, google.maps.OverlayView);
    this.show = __bind(this.show, this);

    this.hide = __bind(this.hide, this);

    this.getPosFromLatLng_ = __bind(this.getPosFromLatLng_, this);

    this.onAdd = __bind(this.onAdd, this);

    this.draw = __bind(this.draw, this);

    this.setContent = __bind(this.setContent, this);

    this.setPosition = __bind(this.setPosition, this);
    this.div_ = null;
    this.latLng = null;
    this.content = '';
    googleInfoDiv.prototype.getProjection = google.maps.OverlayView.prototype.getProjection;
}

googleInfoDiv.prototype.extend = function(obj1, obj2) {
    return (function(object) {
        for (var property in object.prototype) {
            this.prototype[property] = object.prototype[property];
        }
        return this;
    }).apply(obj1, [obj2]);
};

googleInfoDiv.prototype.setPosition = function(latLng) {
    var pos;
    this.latLng = latLng;
    pos = this.getPosFromLatLng_(this.latLng);
    if (this.div_) {
        this.div_.css({
            'top': pos.y + 'px',
            'left': pos.x + 'px'
        });
    }
};

googleInfoDiv.prototype.setContent = function(content) {
    this.content = content;
    if (this.div_) {
        this.div_.html(this.content);
    }
};

googleInfoDiv.prototype.draw = function() {
    var pos;
    if (this.div_) {
        pos = this.getPosFromLatLng_(this.latLng);
        return this.div_.css({
            'top': pos.y + 'px',
            'left': pos.x + 'px'
        });
    }
};

googleInfoDiv.prototype.onAdd = function() {
    var divEl, panes, pos;
    pos = this.getPosFromLatLng_(this.latLng);
    divEl = $('<div style=" width: 5px; height: 5px;position: absolute">' + this.content + '</div>');
    divEl.css({
        'top': (pos.y - 20) + 'px',
        'left': pos.x + 'px'
    });
    this.div_ = divEl;
    panes = this.getPanes();
    $(panes.overlayMouseTarget).append(divEl);
};

googleInfoDiv.prototype.getPosFromLatLng_ = function(LatLng) {
    var pos;
    if(this.getProjection()){
        pos = this.getProjection().fromLatLngToDivPixel(LatLng);
    }
    return pos;
};

googleInfoDiv.prototype.hide = function() {
    if(this.div_){
        this.div_.hide();
    }
};

googleInfoDiv.prototype.show = function() {
    if(this.div_){
        this.div_.show();
    }
};