<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:21
 */
class PopularityDirectionsCity extends Report
{
    private $result;
    private $fromCityId;
    private $_fromCity;
    private $mode;

    public function getFromCity()
    {
        if ($this->_fromCity==null)
            $this->_fromCity = City::model()->findByPk($this->fromCityId);
        return $this->_fromCity;
    }

    public function __construct($fromCityId, $isFrom = true)
    {
        $this->fromCityId = $fromCityId;
        $this->mode = $isFrom; // true = search popularity by from, false = search popularity by to
        $this->result = new PopularityDirectionsFromCityResult($fromCityId, $this->mode);
    }

    public function getMongoCommand()
    {
        $commands = array();
        $map = new MongoCode("
            function() {
                var date = ISODate(this.dateCreate);
                var key = {departureCityId: this.departureCityId, arrivalCityId: this.arrivalCityId };
                emit(key, {count: 1});
            };
        ");
        $reduce = new MongoCode("function(key, values) {
                var sum = 0;
                values.forEach(function(value) {
                    sum += value['count'];
                });
                return {count: sum};
            };
        ");
        $finalize = new MongoCode("function (key, value) {
            return value['count']
        }");
        $commands['mapreduce1'] = array(
            "mapreduce" => Statistic::model()->getCollectionName(),
            "map" => $map,
            "reduce" => $reduce,
            "finalize" => $finalize,
            "query" => array("modelName" => "FlightSearch", 'departureCityId'=>$this->fromCityId),
            "out" =>array('merge' => $this->result->getCollectionName())
        );
        return $commands;
    }

    public function getResult()
    {
        return $this->result;
    }
}

class PopularityDirectionsFromCityResult extends ReportResult
{
    private $departureCity;
    private $arrivalCity;
    private $cityId;
    private $mode;

    public function __construct($cityId, $mode = true)
    {
        $this->mode = $mode; // true = search popularity by from, false = search popularity by to
        $this->cityId = $cityId;
    }

    public function getDepartureCity()
    {
        if ($this->departureCity==null)
            $this->departureCity = City::model()->findByPk($this->_id['departureCityId']);
        return $this->departureCity;
    }

    public function getArrivalCity()
    {
        if ($this->arrivalCity==null)
            $this->arrivalCity = City::model()->findByPk($this->_id['arrivalCityId']);
        return $this->arrivalCity;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getReportName()
    {
        return 'popularity_of_direction_search';
    }

    public function search($caseSensitive = false, $config=array())
    {
        return parent::search($caseSensitive, array(
            'keyField' => 'primaryKey',
            'criteria' => array(
                'conditions' => ($this->mode) ? array('_id.departureCityId' => array('equals' => $this->cityId)) : array('_id.arrivalCityId' => array('equals' => $this->cityId))
            ),
            'sort'=>array(
                'defaultOrder'=>'value desc',
                'attributes'=>array(
                    'value' => array('asc'=>'value asc', 'desc'=>'value desc')
        ))));
    }
}
