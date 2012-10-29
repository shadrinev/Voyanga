# FIXME most likely leaks
ko.bindingHandlers.gMap =
  init: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor()
    #console.log('mapParams',element,value)

    disp = $(element).css('display')
    $(element).css('display','block')
    gMap = new google.maps.Map(element,{'mapTypeControl':false,'panControl':false,'zoomControlOptions':{position: google.maps.ControlPosition.LEFT_TOP,style:google.maps.ZoomControlStyle.SMALL},'streetViewControl':false,'zoom': 3,'mapTypeId': google.maps.MapTypeId.TERRAIN,'center': new google.maps.LatLng(value.lat,value.lng)})
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
  