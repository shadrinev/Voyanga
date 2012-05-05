<?php 
class GDSNemoSoapClient extends SoapClient
{
	public static $bTestBooking = TRUE;
	public static $sTestResponseFileName = NULL;
	public static $sLastRequest;
	public static $sLastResponse;
	public static $sLastHeaders;
	public static $sLastCurlError;
	
	public function __doRequest($sRequest, $sLocation, $sAction, $iVersion, $oneWay = 0)
	{
	    echo $sAction;
	    /*$sXML = '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:ns1="http://srt.mute-lab.com/nemoflights/?version%3D1.0%26for%3DSearchFlights" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <env:Body xmlns:rpc="http://www.w3.org/2003/05/soap-rpc">
    <ns1:searchResponse>
      <rpc:result>ResponseBin</rpc:result>
      <ResponseBin>
        <Response>
          <SearchFlights>
            <Flights>
              <Flight FlightId="369546">
                <WebService>GALILEO</WebService>
                <ValCompany>LX</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">ZRH</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>C3</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1485</FlightNumber>
                    <AircraftType>100</AircraftType>
                    <DepDateTime>2011-03-02T09:50:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:10:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>L</BookingCode>
                    </BookingCodes>
                    <FlightTime>80</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">ZRH</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">DME</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>LX</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1326</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T12:25:00</DepDateTime>
                    <ArrDateTime>2011-03-02T17:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>L</BookingCode>
                    </BookingCodes>
                    <FlightTime>205</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">MXP</DepAirp>
                    <DepTerminal>1</DepTerminal>
                    <ArrAirp CodeType="IATA">ZRH</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>LX</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1617</FlightNumber>
                    <AircraftType>AR1</AircraftType>
                    <DepDateTime>2011-03-04T11:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T12:10:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>T</BookingCode>
                    </BookingCodes>
                    <FlightTime>60</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">ZRH</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>C3</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1486</FlightNumber>
                    <AircraftType>100</AircraftType>
                    <DepDateTime>2011-03-04T12:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:10:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>T</BookingCode>
                    </BookingCodes>
                    <FlightTime>75</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="4750"/>
                    <EquiveFare Currency="RUB" Amount="8075"/>
                    <TotalFare Currency="RUB" Amount="14290"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="252"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="CH" Amount="1512"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="237"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="2952"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="LSWISSCZ" SegNum="1"/>
                      <Tariff Code="LSWISSCZ" SegNum="2"/>
                      <Tariff Code="TSWISSCZ" SegNum="3"/>
                      <Tariff Code="TSWISSCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-02-23T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369547">
                <WebService>GALILEO</WebService>
                <ValCompany>LX</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">ZRH</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>C3</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1485</FlightNumber>
                    <AircraftType>100</AircraftType>
                    <DepDateTime>2011-03-02T09:50:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:10:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>L</BookingCode>
                    </BookingCodes>
                    <FlightTime>80</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">ZRH</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">DME</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>LX</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1326</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T12:25:00</DepDateTime>
                    <ArrDateTime>2011-03-02T17:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>L</BookingCode>
                    </BookingCodes>
                    <FlightTime>205</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">MXP</DepAirp>
                    <DepTerminal>1</DepTerminal>
                    <ArrAirp CodeType="IATA">ZRH</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>2L</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1629</FlightNumber>
                    <AircraftType>100</AircraftType>
                    <DepDateTime>2011-03-04T15:00:00</DepDateTime>
                    <ArrDateTime>2011-03-04T15:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>T</BookingCode>
                    </BookingCodes>
                    <FlightTime>50</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">ZRH</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>C3</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1498</FlightNumber>
                    <AircraftType>100</AircraftType>
                    <DepDateTime>2011-03-04T17:30:00</DepDateTime>
                    <ArrDateTime>2011-03-04T18:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>T</BookingCode>
                    </BookingCodes>
                    <FlightTime>75</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="4750"/>
                    <EquiveFare Currency="RUB" Amount="8075"/>
                    <TotalFare Currency="RUB" Amount="14290"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="252"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="CH" Amount="1512"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="237"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="2952"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="LSWISSCZ" SegNum="1"/>
                      <Tariff Code="LSWISSCZ" SegNum="2"/>
                      <Tariff Code="TSWISSCZ" SegNum="3"/>
                      <Tariff Code="TSWISSCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-02-23T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369548">
                <WebService>GALILEO</WebService>
                <ValCompany>LX</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">ZRH</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>C3</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1485</FlightNumber>
                    <AircraftType>100</AircraftType>
                    <DepDateTime>2011-03-02T09:50:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:10:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>L</BookingCode>
                    </BookingCodes>
                    <FlightTime>80</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">ZRH</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">DME</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>LX</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1326</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T12:25:00</DepDateTime>
                    <ArrDateTime>2011-03-02T17:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>L</BookingCode>
                    </BookingCodes>
                    <FlightTime>205</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">MXP</DepAirp>
                    <DepTerminal>1</DepTerminal>
                    <ArrAirp CodeType="IATA">ZRH</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>LX</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1613</FlightNumber>
                    <AircraftType>AR1</AircraftType>
                    <DepDateTime>2011-03-04T09:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T10:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>T</BookingCode>
                    </BookingCodes>
                    <FlightTime>60</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">ZRH</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>C3</OpAirline>
                    <MarkAirline>LX</MarkAirline>
                    <FlightNumber>1486</FlightNumber>
                    <AircraftType>100</AircraftType>
                    <DepDateTime>2011-03-04T12:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:10:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>T</BookingCode>
                    </BookingCodes>
                    <FlightTime>75</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="4750"/>
                    <EquiveFare Currency="RUB" Amount="8075"/>
                    <TotalFare Currency="RUB" Amount="14290"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="252"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="CH" Amount="1512"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="237"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="2952"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="LSWISSCZ" SegNum="1"/>
                      <Tariff Code="LSWISSCZ" SegNum="2"/>
                      <Tariff Code="TSWISSCZ" SegNum="3"/>
                      <Tariff Code="TSWISSCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-02-23T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369549">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1383</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T10:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2244</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T12:45:00</DepDateTime>
                    <ArrDateTime>2011-03-02T18:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>230</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1713</FlightNumber>
                    <AircraftType>318</AircraftType>
                    <DepDateTime>2011-03-04T15:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:25:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2482</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T18:15:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369550">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1383</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T10:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2244</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T12:45:00</DepDateTime>
                    <ArrDateTime>2011-03-02T18:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>230</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9813</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T18:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4904</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T20:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T22:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369551">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1383</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T10:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2244</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T12:45:00</DepDateTime>
                    <ArrDateTime>2011-03-02T18:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>230</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>XM</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9701</FlightNumber>
                    <AircraftType>32S</AircraftType>
                    <DepDateTime>2011-03-04T13:00:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4902</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T15:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369552">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1383</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T10:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2244</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T12:45:00</DepDateTime>
                    <ArrDateTime>2011-03-02T18:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>230</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1213</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-04T10:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1982</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T13:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369553">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1383</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T10:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T11:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2244</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T12:45:00</DepDateTime>
                    <ArrDateTime>2011-03-02T18:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>230</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9801</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T06:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T08:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4900</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T09:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369554">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2544</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T16:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T21:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1713</FlightNumber>
                    <AircraftType>318</AircraftType>
                    <DepDateTime>2011-03-04T15:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:25:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2482</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T18:15:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369555">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2544</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T16:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T21:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9813</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T18:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4904</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T20:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T22:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369556">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2544</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T16:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T21:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>XM</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9701</FlightNumber>
                    <AircraftType>32S</AircraftType>
                    <DepDateTime>2011-03-04T13:00:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4902</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T15:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369557">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2544</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T16:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T21:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1213</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-04T10:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1982</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T13:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369558">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2544</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T16:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T21:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9801</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T06:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T08:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4900</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T09:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369559">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2483</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T20:35:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:20:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2944</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T23:30:00</DepDateTime>
                    <ArrDateTime>2011-03-03T05:15:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>225</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1713</FlightNumber>
                    <AircraftType>318</AircraftType>
                    <DepDateTime>2011-03-04T15:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:25:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2482</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T18:15:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369560">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2483</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T20:35:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:20:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2944</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T23:30:00</DepDateTime>
                    <ArrDateTime>2011-03-03T05:15:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>225</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9813</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T18:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4904</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T20:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T22:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369561">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2483</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T20:35:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:20:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2944</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T23:30:00</DepDateTime>
                    <ArrDateTime>2011-03-03T05:15:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>225</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>XM</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9701</FlightNumber>
                    <AircraftType>32S</AircraftType>
                    <DepDateTime>2011-03-04T13:00:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4902</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T15:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369562">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2483</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T20:35:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:20:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2944</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T23:30:00</DepDateTime>
                    <ArrDateTime>2011-03-03T05:15:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>225</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1213</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-04T10:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1982</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T13:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369563">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2483</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T20:35:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:20:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2944</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T23:30:00</DepDateTime>
                    <ArrDateTime>2011-03-03T05:15:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>225</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9801</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T06:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T08:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4900</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T09:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369564">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2144</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T16:20:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:00:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1713</FlightNumber>
                    <AircraftType>318</AircraftType>
                    <DepDateTime>2011-03-04T15:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:25:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2482</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T18:15:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:50:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369565">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2144</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T16:20:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:00:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9813</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T18:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T19:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4904</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T20:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T22:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369566">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2144</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T16:20:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:00:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>XM</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9701</FlightNumber>
                    <AircraftType>32S</AircraftType>
                    <DepDateTime>2011-03-04T13:00:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4902</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T15:45:00</DepDateTime>
                    <ArrDateTime>2011-03-04T17:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369567">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2144</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T16:20:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:00:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1213</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-04T10:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>AF</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>1982</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T13:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T14:45:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369568">
                <WebService>GALILEO</WebService>
                <ValCompany>AF</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2D</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4903</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-02T13:05:00</DepDateTime>
                    <ArrDateTime>2011-03-02T14:55:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>110</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2E</DepTerminal>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>D</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>2144</FlightNumber>
                    <AircraftType>321</AircraftType>
                    <DepDateTime>2011-03-02T16:20:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:00:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>220</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">CDG</ArrAirp>
                    <ArrTerminal>2F</ArrTerminal>
                    <OpAirline>AZ</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>9801</FlightNumber>
                    <AircraftType>319</AircraftType>
                    <DepDateTime>2011-03-04T06:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T08:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">CDG</DepAirp>
                    <DepTerminal>2D</DepTerminal>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>OK</OpAirline>
                    <MarkAirline>AF</MarkAirline>
                    <FlightNumber>4900</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-04T09:55:00</DepDateTime>
                    <ArrDateTime>2011-03-04T11:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>M</BookingCode>
                    </BookingCodes>
                    <FlightTime>105</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="false">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="15642"/>
                    <EquiveFare Currency="RUB" Amount="26595"/>
                    <TotalFare Currency="RUB" Amount="36189"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="RI" Amount="285"/>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="FR" Amount="1524"/>
                      <Tax CurCode="RUB" TaxCode="QX" Amount="1375"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="4920"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="MWEEKCZ" SegNum="1"/>
                      <Tariff Code="MWEEKCZ" SegNum="2"/>
                      <Tariff Code="MWEEKCZ" SegNum="3"/>
                      <Tariff Code="MWEEKCZ" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
              <Flight FlightId="369569">
                <WebService>GALILEO</WebService>
                <ValCompany>SU</ValCompany>
                <Segments>
                  <Segment SegNum="1">
                    <DepAirp CodeType="IATA">PRG</DepAirp>
                    <DepTerminal>2</DepTerminal>
                    <ArrAirp CodeType="IATA">BRU</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>SN</OpAirline>
                    <MarkAirline>SN</MarkAirline>
                    <FlightNumber>2814</FlightNumber>
                    <AircraftType>AR8</AircraftType>
                    <DepDateTime>2011-03-02T21:00:00</DepDateTime>
                    <ArrDateTime>2011-03-02T22:30:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>B</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="2">
                    <DepAirp CodeType="IATA">BRU</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">SVO</ArrAirp>
                    <ArrTerminal>E</ArrTerminal>
                    <OpAirline>SU</OpAirline>
                    <MarkAirline>SU</MarkAirline>
                    <FlightNumber>232</FlightNumber>
                    <AircraftType>320</AircraftType>
                    <DepDateTime>2011-03-02T23:55:00</DepDateTime>
                    <ArrDateTime>2011-03-03T05:10:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>Y</BookingCode>
                    </BookingCodes>
                    <FlightTime>195</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="3">
                    <DepAirp CodeType="IATA">LIN</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">BRU</ArrAirp>
                    <ArrTerminal xsi:nil="true"/>
                    <OpAirline>SN</OpAirline>
                    <MarkAirline>SN</MarkAirline>
                    <FlightNumber>3148</FlightNumber>
                    <AircraftType>AR1</AircraftType>
                    <DepDateTime>2011-03-04T12:00:00</DepDateTime>
                    <ArrDateTime>2011-03-04T13:35:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>B</BookingCode>
                    </BookingCodes>
                    <FlightTime>95</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                  <Segment SegNum="4">
                    <DepAirp CodeType="IATA">BRU</DepAirp>
                    <DepTerminal xsi:nil="true"/>
                    <ArrAirp CodeType="IATA">PRG</ArrAirp>
                    <ArrTerminal>2</ArrTerminal>
                    <OpAirline>SN</OpAirline>
                    <MarkAirline>SN</MarkAirline>
                    <FlightNumber>2811</FlightNumber>
                    <AircraftType>AR1</AircraftType>
                    <DepDateTime>2011-03-04T15:10:00</DepDateTime>
                    <ArrDateTime>2011-03-04T16:40:00</ArrDateTime>
                    <StopNum>0</StopNum>
                    <BookingCodes>
                      <BookingCode>B</BookingCode>
                    </BookingCodes>
                    <FlightTime>90</FlightTime>
                    <TimeZone xsi:nil="true"/>
                    <ETicket>true</ETicket>
                  </Segment>
                </Segments>
                <PricingInfo Refundable="true">
                  <PassengerFare Type="ADT" Quantity="1">
                    <BaseFare Currency="CZK" Amount="86184"/>
                    <EquiveFare Currency="RUB" Amount="146515"/>
                    <TotalFare Currency="RUB" Amount="154774"/>
                    <Taxes>
                      <Tax CurCode="RUB" TaxCode="CZ" Amount="893"/>
                      <Tax CurCode="RUB" TaxCode="BE" Amount="1316"/>
                      <Tax CurCode="RUB" TaxCode="EX" Amount="85"/>
                      <Tax CurCode="RUB" TaxCode="HB" Amount="185"/>
                      <Tax CurCode="RUB" TaxCode="IT" Amount="228"/>
                      <Tax CurCode="RUB" TaxCode="MJ" Amount="24"/>
                      <Tax CurCode="RUB" TaxCode="VT" Amount="75"/>
                      <Tax CurCode="RUB" TaxCode="YQ" Amount="5330"/>
                      <Tax CurCode="RUB" TaxCode="YR" Amount="123"/>
                    </Taxes>
                    <Tariffs>
                      <Tariff Code="YIF" SegNum="1"/>
                      <Tariff Code="YIF" SegNum="2"/>
                      <Tariff Code="YIF" SegNum="3"/>
                      <Tariff Code="YIF" SegNum="4"/>
                    </Tariffs>
                    <FareCalc xsi:nil="true"/>
                    <LastTicketDateTime>2011-03-02T23:59:00</LastTicketDateTime>
                  </PassengerFare>
                </PricingInfo>
                <Commission Currency="RUB">0</Commission>
                <Charges Currency="RUB">880</Charges>
              </Flight>
            </Flights>
            <Errors xsi:nil="true"/>
          </SearchFlights>
        </Response>
      </ResponseBin>
    </ns1:searchResponse>
  </env:Body>
</env:Envelope>';*/
	    if($sAction == 'search44'){
	        $sRequest = '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:ns1="http://srt.mute-lab.com/nemoflights/?version%3D1.0%26for%3DSearchFlights">
  <env:Body>
    <ns1:search>
      <RequestBin>
        <Request>
          <SearchFlights>
            <ODPairs Type="OW" Direct="false" AroundDates="0">
              <ODPair>
                <DepDate>2011-07-13T00:00:00</DepDate>
                <DepAirp CodeType="IATA">MOW</DepAirp>
                <ArrAirp CodeType="IATA">PAR</ArrAirp>
              </ODPair>
            </ODPairs>
            <Travellers>
              <Traveller Type="ADT" Count="1"/>
              <Traveller Type="CNN" Count="1"/>
            </Travellers>
            <Restrictions>
              <ClassPref>all</ClassPref>
              <OnlyAvail>true</OnlyAvail>
              <AirVPrefs/>
              <IncludePrivateFare>false</IncludePrivateFare>
              <CurrencyCode>RUB</CurrencyCode>
            </Restrictions>
          </SearchFlights>
        </Request>
        <Source>
          <ClientId>110</ClientId>
          <APIKey>C59661F96B218FFA1523413C3B669D12</APIKey>
          <Language>UA</Language>
          <Currency>RUB</Currency>
        </Source>
      </RequestBin>
    </ns1:search>
  </env:Body>
</env:Envelope>';
	        
	        $sXML = $this->makeSoapRequest($sRequest, $sLocation, $sAction, $iVersion);
	    }else{
	        echo htmlspecialchars($sRequest);
	        $sXML = $this->makeSoapRequest($sRequest, $sLocation, $sAction, $iVersion);
	        //$sXML = '';
	    }
	    
	    return $sXML;
	}
    private function makeSoapRequest($sRequest, $sLocation, $sAction, $iVersion){
		$rCh = curl_init();
		
		curl_setopt($rCh,CURLOPT_POST,(true));
		curl_setopt($rCh,CURLOPT_HEADER,true);
		curl_setopt($rCh,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($rCh,CURLOPT_POSTFIELDS,$sRequest);
		curl_setopt($rCh,CURLOPT_TIMEOUT,190);
		$aHeadersToSend = array();
		$aHeadersToSend[] = "Content-Length: ".strlen($sRequest);
		$aHeadersToSend[] = "Content-Type: text/xml; charset=utf-8";
		$aHeadersToSend[] = "SOAPAction: \"$sAction\"";
		
		curl_setopt($rCh,CURLOPT_HTTPHEADER,$aHeadersToSend);
		curl_setopt($rCh, CURLOPT_URL, $sLocation);
		$sData = curl_exec($rCh);
		//Biletoid_Utils::addLogMessage($sData, '/tmp/curl_response.txt');
		if($sData !== FALSE){
			list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
			if(strpos($sHeaders, 'Continue') !== FALSE){
				list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
			}
			//AlpOnline_SoapClient::$sLastHeaders = $sHeaders;
		}else{
			//AlpOnline_SoapClient::$sLastCurlError = curl_error ($rCh);
		}
		
		return $sData;
	}
}