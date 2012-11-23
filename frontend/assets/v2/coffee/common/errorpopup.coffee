class ErrorPopup extends GenericPopup
  constructor: (code, message=false)->
    id = 'errorpopup-'+code
    super '#' + id, message, true
    ko.processAllDeferredBindingUpdates()

    SizeBox(id);
    ResizeBox(id);

  close: =>
    super
    window.app.navigate window.app.activeModule(), {trigger: true}
