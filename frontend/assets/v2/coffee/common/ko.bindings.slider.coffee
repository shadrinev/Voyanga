# FIXME most likely leaks
ko.bindingHandlers.slider =
  init: (element, valueAccessor) ->
    value = ko.utils.unwrapObservable valueAccessor()
    $(element).selectSlider({})
    selectDiv = $(element).next()
    selectDiv.find('li').each (idx, el)->
      el = $(el)
      if el.data('option-value') == ''+value
        el.addClass('active');
        selectDiv.data('active',el);
        selectDiv.find('.switch').css('left',selectDiv.data('elementWidth')*el.data('ind') + '%');
      else
        el.find('a').css 'color', '#2e333b'
        el.find('a').css 'text-shadow', '0px 1px 0px #FFF'
        el.removeClass('active');


  update: (element, valueAccessor) ->
    #value = ko.utils.unwrapObservable valueAccessor()
    #console.log @slider, value.from, value.to, "!!!!!!!!"
    #@slider("value", value.from, value.to)
  
