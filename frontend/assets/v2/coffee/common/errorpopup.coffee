class ErrorPopup extends GenericPopup
  constructor: (code, message=false, @onclose=false)->
    id = 'errorpopup-'+code
    super '#' + id, message, true
    ko.processAllDeferredBindingUpdates()

    SizeBox(id);
    ResizeBox(id);

  close: =>
    super
    if @onclose
      @onclose()
    else
      # goto index
      window.app.navigate window.app.activeModule(), {trigger: true}
