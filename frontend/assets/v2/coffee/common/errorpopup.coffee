class ErrorPopup extends GenericPopup
  constructor: (code, message=false)->
    id = 'errorpopup-'+code
    super '#' + id #, message
    ko.processAllDeferredBindingUpdates()

    SizeBox(id);
    ResizeBox(id);
