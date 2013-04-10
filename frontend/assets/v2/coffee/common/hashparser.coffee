GRAMMAR = """
start = tour

tour = dests:DESTINATIONS 'rooms/' rooms:ROOMS kv:KEYVALUES {
  return {
    start: dests.start,
    destinations: dests.destinations,
    rooms: rooms,
    extra: kv
  }
}
  / segments:(AVIA_SEGMENT / HOTELS_SEGMENT)+ kv:KEYVALUES{
  return {
    complex: true,
    segments: segments,
    extra: kv
  }
}

DESTINATIONS = start:START_FROM destinations:DESTINATION+ {
  return {start: start, destinations: destinations};
}

AVIA_SEGMENT = 'a/' segment:AVIA { segment.avia = true; return segment;}

HOTELS_SEGMENT =  'h/' segment:HOTELS_WO_KV { segment.hotels = true; return segment}

AVIA = from: IATA '/' to: IATA '/' date:DATE '/' rtDate:(date:DATE '/' {return date})? passangers:PASSANGERS '/' rtLegacy:(date:DATE '/' {return date})?
{
  var result = {
    from: from,
    to: to,
    passangers: passangers,
    dateFrom: date
  }
  if(rtDate!='') {
    result.rtDateFrom = rtDate;
    result.rt = true;
  }
  if(rtLegacy!='') {
    result.rtDateFrom = rtLegacy;
    result.rt = true;
  }
  return result;
}

HOTELS = hotels:HOTELS_WO_KV  kv:KEYVALUES
{
  hotels.extra = kv;
  return hotels;
}

HOTELS_WO_KV = city:IATA '/' fromDate:DATE '/' toDate:DATE '/' rooms:ROOMS {
  return {
    to: city,
    dateFrom: fromDate,
    dateTo: toDate,
    rooms: rooms,
  }
}


START_FROM = from:IATA '/' rt:RT '/'
{  return {
      from: from,
      rt:rt
  }
}

DESTINATION =  to:IATA '/' fromDate:DATE '/' toDate:DATE '/'
{
  return {
      to: to,
      dateFrom: fromDate,
      dateTo: toDate
  }
}
  
IATA
 = code:[A-Z0-9]+ { return code.join("") }

RT
 = [01]

DATE
 = d:DAY '.' m:MONTH '.' y:YEAR {return new Date(y, m - 1,d)}

DAY
 = digits:[0-9]+ {return parseInt(digits.join("")) }

MONTH
 = digits:[0-9]+ {return parseInt(digits.join("")) }

YEAR
 = digits:[0-9]+ {return parseInt(digits.join("")) }

ROOMS 
 = ROOM+

ROOM
 = adults:[1-9] ':' children:[0-9] ':' infants:[0-9] ages:AGES '/'  {
  return {
    adults: parseInt(adults),
    children: parseInt(children),
    infants: parseInt(infants),
    ages:ages
  }
}

PASSANGERS
 = adults:[1-9] '/' children:[0-9] '/' infants:[0-9] {
  return {
    adults: parseInt(adults),
    children: parseInt(children),
    infants: parseInt(infants)
  }
}


AGES = (':' age:[0-9]+ {return parseInt(age.join(""))})*

KEYVALUES = (KEYVALUE)*

// FIXME '/' should not be optional
KEYVALUE = key:[^/]+ '/' val:[^/]+ '/'? { return {key: key.join(""), value: val.join("")} }

"""

if module?
  PEG = require('pegjs')
  export_ = module.exports
else
  export_ = window
export_.GRAMMAR =  GRAMMAR
export_.PEGHashParser =  PEG.buildParser(GRAMMAR)
