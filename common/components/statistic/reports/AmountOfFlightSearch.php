<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:21
 */
class AmountOfFlightSearch extends Report
{
    private $result;

    public function __construct()
    {
        $this->result = new AmountOfFlightSearchResult;
    }

    public function getMongoCommand()
    {
        $commands = array();
        $map = new MongoCode("
            function() {
                var date = ISODate(this.dateCreate);
                var key = {date: date, searchId: this.searchId};
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
        $commands['mapreduce1'] = array(
            "mapreduce" => Statistic::model()->getCollectionName(),
            "map" => $map,
            "reduce" => $reduce,
            "query" => array("modelName" => "FlightSearch"),
            "out" =>$this->result->getCollectionName()
        );
        $map = new MongoCode("
            function() {
                var key = {year: this._id['date'].getFullYear(), month: this._id['date'].getMonth()+1, day: this._id['date'].getDate()};
                emit(key, {count: 1});
            };
        ");
        $commands['mapreduce2'] = array(
            "mapreduce" => $this->result->getCollectionName(),
            "map" => $map,
            "reduce" => $reduce,
            "out" => $this->result->getCollectionName()
        );
        return $commands;
    }

    public function getResult()
    {
        $result = AmountOfFlightSearchResult::model();
        return $result;
    }
}

class AmountOfFlightSearchResult extends ReportResult
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getReportName()
    {
        return 'amount_of_flight_search';
    }
}
