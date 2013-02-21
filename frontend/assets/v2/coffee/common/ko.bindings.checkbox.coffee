checkVoybox = (el) ->
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
        if _.isFunction checked
          checked(true)
        else
          $(element).attr 'checked', 'checked'
      else
        if _.isFunction checked
          checked(false)
        else
          $(element).removeAttr 'checked'
        uncheckVoybox(el)

    el.after(new_el)
    el.hide()


  update: (element, valueAccessor) ->
    label = ko.utils.unwrapObservable valueAccessor().label
    checked = ko.utils.unwrapObservable valueAccessor().checked
    el = $(element).next()
    el.find('.ui-label').html(label)
    if checked
      checkVoybox(el)
      if (_.isFunction(valueAccessor().checked))
        valueAccessor().checked(1)
    else
      uncheckVoybox(el)
      if (_.isFunction(valueAccessor().checked))
        valueAccessor().checked(0)