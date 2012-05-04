<?php
class FlightSearchParams {
    public $aRoutes;
    public $flight_class;
    public $adult_count;
    public $child_count;
    public $infant_count;
    private $key;
    
    public function addRoute( $aRouteParams ) {
        $oRoute = new Route();
        if ( $aRouteParams['departure_city_id'] ) {
            $oRoute->departure_city_id = $aRouteParams['departure_city_id'];
        } else {
            throw new CException( Yii::t( 'application', 'Required param departure_city_id not set' ) );
        }
        
        if ( $aRouteParams['departure_city_id'] ) {
            $oRoute->arrival_city_id = $aRouteParams['arrival_city_id'];
        } else {
            throw new CException( Yii::t( 'application', 'Required param arrival_city_id not set' ) );
        }
        if ( $aRouteParams['departure_date'] ) {
            if ( strpos( $aRouteParams['departure_date'], '.' ) !== false ) {
                list( $dd, $mm, $yy ) = explode( '.', $aRouteParams['departure_date'] );
            } elseif ( strpos( $aRouteParams['departure_date'], '-' ) !== false ) {
                list( $yy, $mm, $dd ) = explode( '.', $aRouteParams['departure_date'] );
            } else {
                throw new CException( Yii::t( 'application', 'departure_date format invalid. Need dd.mm.yyyy or yyyy-mm-dd' ) );
            }
            if ( !checkdate( $mm, $dd, $yy ) ) {
                throw new CException( Yii::t( 'application', 'departure_date parametr - date incorrect ' ) );
            }
            if ( $aRouteParams['adult_count'] ) {
                $oRoute->adult_count = intval( $aRouteParams['adult_count'] );
                $this->adult_count = $oRoute->adult_count;
            }
            if ( $aRouteParams['child_count'] ) {
                $oRoute->child_count = intval( $aRouteParams['child_count'] );
                $this->child_count = $oRoute->child_count;
            }
            if ( $aRouteParams['infant_count'] ) {
                $oRoute->infant_count = intval( $aRouteParams['infant_count'] );
                $this->infant_count = $oRoute->infant_count;
            }
            $oRoute->departure_date = "{$yy}-{$mm}-{$dd}";
        } else {
            throw new CException( Yii::t( 'application', 'Required param departure_date not set' ) );
        }
        if ( ( $oRoute->adult_count + $oRoute->child_count ) <= 0 ) {
            throw new CException( Yii::t( 'application', 'Passengers count must be more then zero' ) );
        }
        if ( ( $oRoute->adult_count + $oRoute->child_count ) < $oRoute->infant_count ) {
            throw new CException( Yii::t( 'application', 'Infants count must be equal or less then (adult + child) count' ) );
        }
        $this->aRoutes[] = $oRoute;
    }
    
    public function __get( $name ) {
        if ( $name === 'key' ) {
            $sKey = $this->flight_class . json_encode( $this->aRoutes );
            return md5( $sKey );
        } else {
            return $this->$name;
        }
    }
    
    public function checkValid() {
        $bValid = true;
        if ( !$this->flight_class ) {
            $bValid = false;
        }
        if ( count( $this->aRoutes ) <= 0 ) {
            $bValid = false;
        }
        return $bValid;
    }
}