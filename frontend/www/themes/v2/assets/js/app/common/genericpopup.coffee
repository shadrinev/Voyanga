class GenericPopup
  constructor: (@id, data)->
    $('body').prepend('<div id="popupOverlay"></div>')
    el = $($(@id+'-template').html())
    $('body').prepend(el)
    # FIXME Binging against bingingContext. ULTRABAD
    if data['$data']
      if !data['$data']['data']
        data['$data'] = {data: data['$data']}
      data['$data']['close']=@close

      # FIXME FIXM FIXME
      ko.applyBindings(data, el[0])
    else
      ko.applyBindings({data: data, close:@close}, el[0])
    ko.processAllDeferredBindingUpdates()
    $(window).keyup (e) =>
      if e.keyCode == 27
        @close()

    $('#popupOverlay').click =>
      @close()


  close: =>
    $(window).unbind 'keyup'
    $(@id).remove()
    $('#popupOverlay').remove()
