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
    expect(result).toEqual {start: {from:'MOW', rt:'1'}, destinations:[{to: 'LED', dateFrom: new Date(2013,8,5), dateTo: new Date(2013,8,7)}]}

    result = parser.parse('MOW/0/LED/5.9.2013/7.9.2013/', 'DESTINATIONS')
    expect(result).toEqual {start: {from:'MOW', rt:'0'}, destinations:[{to: 'LED', dateFrom: new Date(2013,8,5), dateTo: new Date(2013,8,7)}]}


  it 'can parse multiple destination', ->
    result = parser.parse('MOW/1/LED/5.9.2013/7.9.2013/PAR/9.9.2013/11.9.2013/', 'DESTINATIONS')
    expect(result).toEqual {
      start: {from:'MOW', rt:'1'},
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


describe 'PEG AVIA rule', ->
  it 'can parse one way flight params', ->
    result = parser.parse("LED/MOW/27.4.2013/1/0/0/", 'AVIA')
    expect(result).toEqual {from: "LED", to: "MOW", dateFrom: new Date(2013,3,27), passangers: {adults:1, children:0, infants: 0}} 

  it 'can parse RT flight params', ->
    result = parser.parse("LED/MOW/27.4.2013/5.5.2013/1/2/3/", 'AVIA')
    expect(result).toEqual {from: "LED", to: "MOW", dateFrom: new Date(2013,3,27), rtDateFrom: new Date(2013, 4, 5), rt:true, passangers: {adults:1, children:2, infants: 3}} 

  it 'can parse legacy RT flight params', ->
    result = parser.parse("LED/MOW/27.4.2013/1/2/3/5.5.2013/", 'AVIA')
    expect(result).toEqual {from: "LED", to: "MOW", dateFrom: new Date(2013,3,27), rtDateFrom: new Date(2013, 4, 5), rt:true, passangers: {adults:1, children:2, infants: 3}}

describe 'PEG HOTELS rule', ->
  it 'can parse single room search', ->
    result = parser.parse("MOW/19.9.2013/21.9.2013/2:0:0/", 'HOTELS')
    expect(result).toEqual {to: "MOW", dateFrom: new Date(2013, 8, 19), dateTo: new Date(2013, 8, 21), rooms: [{adults:2, children:0, infants:0, ages: []}], extra: []}

  it 'can parse single room search with keywords', ->
    result = parser.parse("MOW/19.9.2013/21.9.2013/2:0:0/hotelId/31337/", 'HOTELS')
    expect(result).toEqual {to: "MOW", dateFrom: new Date(2013, 8, 19), dateTo: new Date(2013, 8, 21), rooms: [{adults:2, children:0, infants:0, ages: []}], extra: [{key:'hotelId', value: '31337'}]}


  it 'can parse multiple rooms search', ->
    result = parser.parse("MOW/19.9.2013/21.9.2013/2:0:0/1:1:1:91/", 'HOTELS')
    expect(result).toEqual {to: "MOW", dateFrom: new Date(2013, 8, 19), dateTo: new Date(2013, 8, 21), rooms: [{adults:2, children:0, infants:0, ages: []}, {adults:1, children:1, infants:1, ages: [91]}], extra: []}

  it 'can parse multiple rooms search with keywords', ->
    result = parser.parse("MOW/19.9.2013/21.9.2013/2:0:0/1:1:1:91/hotelId/91238/", 'HOTELS')
    expect(result).toEqual {to: "MOW", dateFrom: new Date(2013, 8, 19), dateTo: new Date(2013, 8, 21), rooms: [{adults:2, children:0, infants:0, ages: []}, {adults:1, children:1, infants:1, ages: [91]}], extra: [{key: 'hotelId', value: '91238'}]}

describe 'PEG COMPLEX TOUR rule', ->
  it 'can parse single avia segment', ->
    result = parser.parse("a/MOW/LED/17.4.2013/18.4.2013/1/0/0/", 'tour') 
    expect(result).toEqual {complex: true, segments: [{avia: true,from: "MOW", to: "LED", dateFrom: new Date(2013,3,17), rtDateFrom: new Date(2013, 3, 18), rt:true, passangers: {adults:1, children:0, infants: 0}}], extra: []}

    result = parser.parse("a/MOW/LED/17.4.2013/1/0/0/", 'tour') 
    expect(result).toEqual {complex: true, segments: [{avia: true,from: "MOW", to: "LED", dateFrom: new Date(2013,3,17), passangers: {adults:1, children:0, infants: 0}}], extra:[]}
    
  it 'can parse multiple avia segments', ->
    result = parser.parse("a/MOW/LED/17.4.2013/18.4.2013/1/0/0/a/LED/PAR/20.4.2013/22.4.2013/1/3/2/", 'tour') 
    expect(result).toEqual {complex: true, segments: [{avia: true,from: "MOW", to: "LED", dateFrom: new Date(2013,3,17), rtDateFrom: new Date(2013, 3, 18), rt:true, passangers: {adults:1, children:0, infants: 0}}, {avia: true, from: "LED", to: "PAR", dateFrom: new Date(2013,3,20), rtDateFrom: new Date(2013,3,22),  rt:true, passangers: {adults:1, children:3, infants: 2}}], extra:[]}
    