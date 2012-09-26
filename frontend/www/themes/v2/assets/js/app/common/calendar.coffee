# Adapter class for voyanga.calendar
class Calendar extends Backbone.Events
  constructor: (module, panel) ->
    window.voyanga_debug('CALENDAR', 'constructor', panel())
    VoyangaCalendarStandart.init panel()

  minimize: ->

  maximize: ->


