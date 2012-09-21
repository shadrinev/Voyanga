checkVoybox = (el) ->
#  if button == 'radio'
#    $('.radio').removeClass 'on'
    el.find('.ui-control').addClass('on')
uncheckVoybox = (el) ->
  el.find('.ui-control').removeClass('on');		

# FIXME most likely leaks
ko.bindingHandlers.checkbox =
  init: (element, valueAccessor) ->
    label = ko.utils.unwrapObservable valueAccessor().label
    checked = valueAccessor().checked

    el = $(element)
    classCheck = 'checkbox'#el.attr('type');
    htm = '';
    htm += '<label class="ui-hover cF">';
    htm += '<div class="ui-control '+classCheck+'"></div>';
    htm += '<div class="fl">';
    htm += '<div class="ui-label">'+label+'</div>';
    htm += '</div>';
    htm += '</label>';
    new_el = $(htm)
    new_el.click ->
      el = $(@)
      if el.find('.ui-control').hasClass('on') != true
        checkVoybox(el)
        checked(true)
      else
        uncheckVoybox(el)

    el.after(new_el)
    el.hide()


  update: (element, valueAccessor) ->
    checked = ko.utils.unwrapObservable valueAccessor().checked
    el = $(element).next()
    if checked
      checkVoybox(el)
    else
      uncheckVoybox(el)