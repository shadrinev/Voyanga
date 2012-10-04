# Common model for panels search params,
class SearchParams
  constructor: ->
    @date = ko.observable ''
    @adults = ko.observable(1).extend({integerOnly: 'adult'})
    @children = ko.observable(0).extend({integerOnly: true})
    @infants = ko.observable(0).extend({integerOnly: 'infant'})