$SABRE_PAYMENT_QUERY = <<<DATA
<AirTicketRQ ReturnHostCommand="true" Version="2.0.0">
<OptionalQualifiers>
<FlightQualifiers>
<VendorPrefs>
<Airline Code="SU"/>
</VendorPrefs>
</FlightQualifiers>
<FOP_Qualifiers>
<BasicFOP>
<CC_Info Suppress="true">
<PaymentCard CardSecurityCode="{{CardSecurityCode}}" Code="{{Code}}" ExpireDate="{{ExpireDate}}" Number="{{Number}}"/>
</CC_Info>
</BasicFOP>
</FOP_Qualifiers>
<MiscQualifiers>
<Ticket Type="ETR"/>
</MiscQualifiers>
<PricingQualifiers>
<NameSelect NameNumber="1.1"/>
<PriceQuote Number="1"/>
</PricingQualifiers>
</OptionalQualifiers>
g</AirTicketRQ>
DATA;

class Payments_Channel_Sabre {
    protected $name = 'gds_sabre';

    public function formParams() {
        $params = parent::formParams();
        //! FIXME: implement commission split
        $params['Commission'] = sprintf("%.2f", $this->booker->flightVoyage->commission);
        $params['PNR'] = $thi->booker->pnr;
        $params['query'] = $SABRE_PAYMENT_QUERY;
        return $params;
    }
}
