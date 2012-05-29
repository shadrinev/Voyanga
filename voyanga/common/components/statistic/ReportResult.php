<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:05
 */
abstract class ReportResult extends EMongoDocument
{
    public $_id;
    public $value;

    abstract function getReportName();

    public function getPrimaryKey()
    {
        return implode('_', $this->_id);
    }

    // As always define the getCollectionName() and model() methods !
    public function getCollectionName()
    {
        return 'result_'.$this->getReportName();
    }

    public function search($caseSensitive = false, $config = array())
    {
        $criteria = $this->getDbCriteria();

        foreach($this->getSafeAttributeNames() as $attribute)
        {
            if($this->$attribute !== null && $this->$attribute !== '')
            {
                if(is_array($this->$attribute) || is_object($this->$attribute))
                    $criteria->$attribute = $this->$attribute;
                else if(preg_match('/^(?:\s*(<>|<=|>=|<|>|=|!=|==))?(.*)$/',$this->$attribute,$matches))
                {
                    $op = $matches[1];
                    $value = $matches[2];

                    if($op === '=') $op = '==';

                    if($op !== '')
                        call_user_func(array($criteria, $attribute), $op, is_numeric($value) ? floatval($value) : $value);
                    else
                        $criteria->$attribute = new MongoRegex($caseSensitive ? '/'.$this->$attribute.'/' : '/'.$this->$attribute.'/i');
                }
            }
        }

        $this->setDbCriteria($criteria);

        return new EMongoDocumentDataProvider($this, $config);
    }
}
