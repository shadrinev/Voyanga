PEG = require 'pegjs'
HashParser = require '../coffee/common/hashparser'
parser = PEG.buildParser(HashParser.GRAMMAR)

describe 'PEG ROOMS rule', ->
  it 'can parse single room', ->
    result = parser.parse('1:2:3/', 'ROOMS')
    expect(result).toEqual [{adults:1, children:2, infants:3, ages: []}]

  it 'can parse single room with child ages', ->
    result = parser.parse('3:1:9:12:99/', 'ROOMS')
    expect(result).toEqual [{adults:3, children:1, infants:9, ages: [12,99]}]

  it 'can parse multiple rooms', ->
    result = parser.parse('1:2:3/4:5:6/', 'ROOMS')
    expect(result).toEqual [{adults:1, children:2, infants:3, ages: []},{adults:4, children:5, infants:6, ages: []}]

  it 'can parse multilple room (some of which are) with child ages', ->
    result = parser.parse('1:2:3:12:99/4:5:6:1:19/', 'ROOMS')
    expect(result).toEqual [{adults:1, children:2, infants:3, ages: [12,99]}, {adults:4, children:5, infants:6, ages: [1,19]}]

    result = parser.parse('1:2:0/4:5:6:1:19/', 'ROOMS')
    expect(result).toEqual [{adults:1, children:2, infants:0, ages: []}, {adults:4, children:5, infants:6, ages: [1,19]}]


describe 'PEG DESTINATIONS rule', ->
  it 'can parse single destination', ->
    result = parser.parse('MOW/1/LED/5.9.2013/7.9.2013/', 'DESTINATIONS')
    expect(result).toEqual {start: {from:'MOW', return:'1'}, destinations:[{to: 'LED', dateFrom: new Date(2013,8,5), dateTo: new Date(2013,8,7)}]}

    result = parser.parse('MOW/0/LED/5.9.2013/7.9.2013/', 'DESTINATIONS')
    expect(result).toEqual {start: {from:'MOW', return:'0'}, destinations:[{to: 'LED', dateFrom: new Date(2013,8,5), dateTo: new Date(2013,8,7)}]}


  it 'can parse multiple destination', ->
    result = parser.parse('MOW/1/LED/5.9.2013/7.9.2013/PAR/9.9.2013/11.9.2013/', 'DESTINATIONS')
    expect(result).toEqual {
      start: {from:'MOW', return:'1'},
      destinations:[
        {to: 'LED', dateFrom: new Date(2013,8,5), dateTo: new Date(2013,8,7)},
        {to: 'PAR', dateFrom: new Date(2013,8,9), dateTo: new Date(2013,8,11)}]
    }


describe 'PEG KEYVALUES rule', ->
  it 'can be optional', ->
    result = parser.parse("", 'KEYVALUES')
    expect(result).toEqual []

  it 'can parse single KV pair', ->
    result = parser.parse("FOO/BAR/", 'KEYVALUES')
    expect(result).toEqual [{key: "FOO", value: "BAR"}]

  it 'can parse multiple KV pairs', ->
    result = parser.parse("FOO/BAR/KEY/VAL/", 'KEYVALUES')
    expect(result).toEqual [{key: "FOO", value: "BAR"}, {key: "KEY", value: "VAL"}]


  it 'can have no trailing slash', ->
    result = parser.parse("FOO/BAR", 'KEYVALUES')
    expect(result).toEqual [{key: "FOO", value: "BAR"}]

    result = parser.parse("FOO/BAR/KEY/VAL", 'KEYVALUES')
    expect(result).toEqual [{key: "FOO", value: "BAR"}, {key: "KEY", value: "VAL"}]