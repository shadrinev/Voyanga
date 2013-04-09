ko.bindingHandlers.autocomplete =
  init: (element, valueAccessor) ->
    $(element).bind "focus", ->
      $(element).select()
    $(element).typeahead
      name: 'cities'
      limit: 5 # The max number of suggestions from the dataset to display for a given query
      prefetch: '/js/cities.json'
      remote: window.apiEndPoint + "helper/autocomplete/" + valueAccessor().source + '/query/%QUERY' # Страница для обработки запросов автозаполнения
      template: '<div title="{{value}}"><span class="city">{{name}}, </span><span class="country">{{country}}</span><span class="code">{{code}}</span></div>'
      engine: Hogan

    $(element).on 'typeahead:selected typeahead:autocompleted', (e, data) -> # Callback функция, срабатывающая на выбор одного из предложенных вариантов,
      valueAccessor().iata(data.code)
      valueAccessor().readable(data.name)
      valueAccessor().readableGen(data.nameGen)
      valueAccessor().readableAcc(data.nameAcc)
      valueAccessor().readablePre(data.namePre)
      $(element).val(data.name)
      $(element).parent().siblings('input.input-path').val(data.value + ', ' + data.country)
      if ((!$(element).is('.arrivalCity')) && ($('input.arrivalCity').length>0))
        $('input.arrivalCity.second-path').focus()
    $(element).on 'typeahead:over', (e, data) -> # Callback функция, срабатывающая на выбор одного из предложенных вариантов,
      $(element).parent().siblings('input.input-path').val(data.value + ', ' + data.country)
    $(element).on 'typeahead:reset', (e) -> # Callback функция, срабатывающая на выбор одного из предложенных вариантов,
      $(element).parent().siblings('input.input-path').val('')

    $(element).on "keyup", (e) ->
      if ((e.keyCode == 8) || (e.keyCode == 46))
        valueAccessor().iata('')
        valueAccessor().readable('')
        valueAccessor().readableGen('')
        valueAccessor().readableAcc('')
        valueAccessor().readablePre('')


  update: (element, valueAccessor) =>
    iataCode = valueAccessor().iata()
    content = valueAccessor().readable()
    if content == undefined then content=iataCode
    _.each $(element).typeahead("setQueryInternal", content).data('ttView').datasets, (dataset)->
      dataset.getSuggestions iataCode, (s) ->
        if (s.length>0)
          _.each s, (s)->
            if (s.datum.code==iataCode)
              console.log "Updating element. Found", s
              data = s.datum
              valueAccessor().readable(data.name)
              valueAccessor().readableGen(data.nameGen)
              valueAccessor().readableAcc(data.nameAcc)
              valueAccessor().readablePre(data.namePre)
              if (($(element).val().length==0) || ($(element).val() != data.name))
                $(element).val(data.name)
                $(element).parent().siblings('input.input-path').val(data.value + ', ' + data.country)
