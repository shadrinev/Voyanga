var HotelResult, HotelsResultSet, Room, RoomSet, STARS_VERBOSE;

STARS_VERBOSE = ['one', 'two', 'three', 'four', 'five'];

Room = (function() {

  function Room(data) {
    this.name = data.showName;
    this.meal = data.meal;
    this.hasMeal = this.meal !== 'Без питания' && this.meal !== 'Не известно';
  }

  return Room;

})();

RoomSet = (function() {

  function RoomSet(data) {
    var room, _i, _len, _ref;
    this.price = data.rubPrice;
    this.rooms = [];
    _ref = data.rooms;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      room = _ref[_i];
      this.rooms.push(new Room(room));
    }
  }

  return RoomSet;

})();

HotelResult = (function() {

  function HotelResult(data) {
    _.extend(this, Backbone.Events);
    this.hotelName = data.hotelName;
    this.address = "Урюпинкс, 92-120";
    this.description = "Foo bar description.\nFoo bar description. Foo bar description. Foo bar description.\nFoo bar description. Foo bar description. Foo bar description.";
    this.stars = STARS_VERBOSE[data.categoryId - 1];
    this.rating = data.rating;
    this.roomSets = [];
    this.push(data);
  }

  HotelResult.prototype.push = function(data) {
    return this.roomSets.push(new RoomSet(data));
  };

  return HotelResult;

})();

HotelsResultSet = (function() {

  function HotelsResultSet(rawHotels) {
    var hotel, key, result, _i, _len, _ref;
    this._results = {};
    for (_i = 0, _len = rawHotels.length; _i < _len; _i++) {
      hotel = rawHotels[_i];
      key = hotel.hotelId;
      if (this._results[key]) {
        this._results[key].push(hotel);
      } else {
        result = new HotelResult(hotel);
        this._results[key] = result;
      }
    }
    this.data = [];
    _ref = this._results;
    for (key in _ref) {
      result = _ref[key];
      this.data.push(result);
    }
  }

  return HotelsResultSet;

})();
