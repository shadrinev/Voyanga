ko.bindingHandlers.autocomplete =
  init: (element, valueAccessor) ->
    setTimeout ()=>
      $(element).bind "focus", ->
        $(element).change()
      $(element).typeahead
        name: 'cities', # The string used to identify the dataset. Used by typeahead.js to cache intelligently
        limit: 10 # The max number of suggestions from the dataset to display for a given query
        prefetch: '/js/cities.json'
        template: '<div title="{{value}}"><span class="city">{{name}}, </span><span class="country">{{country}}</span><span class="code">{{code}}</span></div>'
        engine: Hogan
      $(element).on 'typeahead:selected', (e, data) -> # Callback функция, срабатывающая на выбор одного из предложенных вариантов,
          valueAccessor().iata(data.code)
          valueAccessor().readable(data.name)
          valueAccessor().readableGen(data.nameGen)
          valueAccessor().readableAcc(data.nameAcc)
          valueAccessor().readablePre(data.namePre)
          $(element).val(data.name)
          $(element).siblings('input.input-path').val(data.value + ', ' + data.country)
    , 500

  update: (element, valueAccessor) =>
    iataCode = valueAccessor().iata()

    url = (code) ->
      result = window.apiEndPoint + '/helper/autocomplete/citiesReadable?'
      params = []
      params.push 'codes[0]=' + code
      result += params.join "&"
      return result

    handleResults = (data) ->
      valueAccessor().readable(data[iataCode].name)
      valueAccessor().readableGen(data[iataCode].nameGen)
      valueAccessor().readableAcc(data[iataCode].nameAcc)
      valueAccessor().readablePre(data[iataCode].namePre)
      if ($(element).val().length == 0)
        $(element).val(data[iataCode].name)
        $(element).siblings('input.input-path').val(data[iataCode].label)

    if (iataCode.length > 0)
      $.ajax
        url: url iataCode
        dataType: 'json'
        success: handleResults

