# FIXME most likely leaks
ko.bindingHandlers.gMap =
  init: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor()
    #console.log('mapParams',element,value)

    disp = $(element).css('display')
    $(element).css('display','block')
    gMap = new google.maps.Map(element,{'zoom': 2,'mapTypeId': google.maps.MapTypeId.ROADMAP,'center': new google.maps.LatLng(value.lat,value.lng)})
    window.setTimeout(
      ->

        #console.log('gmo',gMap)
        $(element).css('display',disp)
      , 700
    )


  update: (element, valueAccessor) ->
    #value = ko.utils.unwrapObservable valueAccessor()
    #console.log @slider, value.from, value.to, "!!!!!!!!"
    #@slider("value", value.from, value.to)
  