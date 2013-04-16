# Базовая имплементация методов по работе с комнатами
class RoomsContainerMixin
  getRooms: ->
    return @rooms()
    
  addRoom: =>
    if @rooms.length == 4
      return
    if @overall() > 8
      return
    @rooms.push new SpRoom(@)

  removeRoom: (room)->
    # You should not be allower to remove last room left
    if @rooms().length == 1
      return

    @rooms.splice @rooms.indexOf(room),1

  onOverallChanged: (clb)->
    @overall.subscribe clb

implement(RoomsContainerMixin, IRoomsContainer)
