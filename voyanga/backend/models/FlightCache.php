<?php
class FlightCache extends CommonFlightCache
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightCache the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        $currentSql = '';
        if ($this->isNewRecord)
            $currentSql = "`createdAt` = NOW(), ";

        $query = "INSERT INTO ".$this->tableName()." SET "
            ."`dateFrom` = '".$this->dateFrom."', "
            ."`dateBack` = '".$this->dateBack."', "
            ."`from` = '".$this->from."', "
            ."`to` = '".$this->to."', "
            ."`priceBestPrice` = '".$this->priceBestPrice."', "
            ."`transportBestPrice` = '".$this->transportBestPrice."', "
            ."`validatorBestPrice` = '".$this->validatorBestPrice."', "
            ."`durationBestPrice` = '".$this->durationBestPrice."', "
            ."`priceBestTime` = '".$this->priceBestTime."', "
            ."`transportBestTime` = '".$this->transportBestTime."', "
            ."`validatorBestTime` = '".$this->validatorBestTime."', "
            ."`durationBestTime` = '".$this->durationBestTime."', "
            ."`priceBestPriceTime` = '".$this->priceBestPriceTime."', "
            ."`transportBestPriceTime` = '".$this->transportBestPriceTime."', "
            ."`validatorBestPriceTime` = '".$this->validatorBestPriceTime."', "
            ."`durationBestPriceTime` = '".$this->durationBestPriceTime."', "
            .$currentSql
            ."`updatedAt` = NOW() "
            ." ON DUPLICATE KEY UPDATE "
            ."`priceBestPrice` = '".$this->priceBestPrice."', "
            ."`transportBestPrice` = '".$this->transportBestPrice."', "
            ."`validatorBestPrice` = '".$this->validatorBestPrice."', "
            ."`durationBestPrice` = '".$this->durationBestPrice."', "
            ."`priceBestTime` = '".$this->priceBestTime."', "
            ."`transportBestTime` = '".$this->transportBestTime."', "
            ."`validatorBestTime` = '".$this->validatorBestTime."', "
            ."`durationBestTime` = '".$this->durationBestTime."', "
            ."`priceBestPriceTime` = '".$this->priceBestPriceTime."', "
            ."`transportBestPriceTime` = '".$this->transportBestPriceTime."', "
            ."`validatorBestPriceTime` = '".$this->validatorBestPriceTime."', "
            ."`durationBestPriceTime` = '".$this->durationBestPriceTime."', "
            ."`updatedAt` = NOW() ";
        $command = Yii::app()->db->createCommand($query);
        $command->execute();
        return false;
    }
}