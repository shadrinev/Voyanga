<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 07.09.12 11:12
 */
class CityManager extends CApplicationComponent
{
    static public function getCitiesWithAirports($query)
    {
        $currentLimit = appParams('autocompleteLimit');
        $items = Yii::app()->cache->get('autocompleteCityForFlight' . $query);
        if (!$items)
        {
            $items = array();
            $cityIds = array();

            if (strlen($query) == 3)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                $criteria->params[':code'] = $query;
                $criteria->addCondition('t.code = :code');
                $criteria->addCondition('t.countAirports > 0');
                $criteria->with = 'country';
                /** @var  City[] $cities  */
                $cities = City::model()->findAll($criteria);

                if ($cities)
                {
                    foreach ($cities as $city)
                    {
                        $items[] = array(
                            'id' => $city->primaryKey,
                            'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                            'value' => self::parseTemplate('{localRu}', $city),
                        );
                        $cityIds[$city->id] = $city->id;
                    }
                }
                $currentLimit -= count($items);
            }
            $criteria = new CDbCriteria();
            $criteria->limit = $currentLimit;
            $criteria->params[':localRu'] = $query . '%';
            $criteria->params[':localEn'] = $query . '%';

            $criteria->addCondition('t.localRu LIKE :localRu OR t.localEn LIKE :localEn');
            $criteria->addCondition('t.countAirports > 0');
            if ($cityIds)
            {
                $criteria->addCondition('t.id NOT IN (' . join(',', $cityIds) . ')');
            }
            $criteria->with = 'country';
            $criteria->order = 'country.position desc, t.position desc';
            $cities = City::model()->findAll($criteria);

            if ($cities)
            {
                foreach ($cities as $city)
                {
                    $items[] = array(
                        'id' => $city->primaryKey,
                        'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                        'value' => self::parseTemplate('{localRu}', $city),
                    );
                    $cityIds[$city->id] = $city->id;
                }
            }
            $currentLimit -= count($items);
            if ($currentLimit)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                if (UtilsHelper::countRussianCharacters($query) > 0)
                {
                    $nameRu = $query;
                }
                else
                {

                    $nameRu = UtilsHelper::cityNameToRus($query);
                }
                $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);

                if ($metaphoneRu)
                {
                    $criteria->params[':metaphoneRu'] = $metaphoneRu;

                    $criteria->addCondition('t.metaphoneRu = :metaphoneRu');
                    $criteria->addCondition('t.countAirports > 0');
                    if ($cityIds)
                    {
                        $criteria->addCondition('t.id NOT IN (' . join(',', $cityIds) . ')');
                    }
                    $criteria->with = 'country';
                    $criteria->order = 'country.position desc, t.position desc';
                    $cities = City::model()->findAll($criteria);

                    if ($cities)
                    {
                        foreach ($cities as $city)
                        {
                            $items[] = array(
                                'id' => $city->primaryKey,
                                'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                                'value' => self::parseTemplate('{localRu}', $city),
                            );
                            $cityIds[$city->id] = $city->id;
                        }
                    }
                    $currentLimit -= count($items);
                }
            }
            Yii::app()->cache->set('autocompleteCityForFlight' . $query, $items, appParams('autocompleteCacheTime'));
        }
        return $items;
    }

    static public function getCitiesWithHotels($query)
    {
        $currentLimit = appParams('autocompleteLimit');
        $items = Yii::app()->cache->get('autocompleteCityForHotel' . $query);
        if (!$items)
        {
            $items = array();
            $cityIds = array();

            if (strlen($query) == 3)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                $criteria->params[':code'] = $query;
                $criteria->addCondition('t.code = :code');
                $criteria->addCondition('t.hotelbookId > 0');
                $criteria->with = 'country';
                /** @var  City[] $cities  */
                $cities = City::model()->findAll($criteria);

                if ($cities)
                {
                    foreach ($cities as $city)
                    {
                        $items[] = array(
                            'id' => $city->primaryKey,
                            'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                            'value' => self::parseTemplate('{localRu}', $city),
                        );
                        $cityIds[$city->id] = $city->id;
                    }
                }
                $currentLimit -= count($items);
            }
            $criteria = new CDbCriteria();
            $criteria->limit = $currentLimit;
            $criteria->params[':localRu'] = $query . '%';
            $criteria->params[':localEn'] = $query . '%';

            $criteria->addCondition('t.localRu LIKE :localRu OR t.localEn LIKE :localEn');
            $criteria->addCondition('t.hotelbookId > 0');
            if ($cityIds)
            {
                $criteria->addCondition('t.id NOT IN (' . join(',', $cityIds) . ')');
            }
            $criteria->with = 'country';
            $criteria->order = 'country.position desc, t.position desc';
            $cities = City::model()->findAll($criteria);

            if ($cities)
            {
                foreach ($cities as $city)
                {
                    $items[] = array(
                        'id' => $city->primaryKey,
                        'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                        'value' => self::parseTemplate('{localRu}', $city),
                    );
                    $cityIds[$city->id] = $city->id;
                }
            }
            $currentLimit -= count($items);
            if ($currentLimit)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                if (UtilsHelper::countRussianCharacters($query) > 0)
                {
                    $nameRu = $query;
                }
                else
                {

                    $nameRu = UtilsHelper::cityNameToRus($query);
                }
                $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);

                if ($metaphoneRu)
                {
                    $criteria->params[':metaphoneRu'] = $metaphoneRu;

                    $criteria->addCondition('t.metaphoneRu = :metaphoneRu');
                    $criteria->addCondition('t.hotelbookId > 0');
                    if ($cityIds)
                    {
                        $criteria->addCondition('t.id NOT IN (' . join(',', $cityIds) . ')');
                    }
                    $criteria->with = 'country';
                    $criteria->order = 'country.position desc, t.position desc';
                    $cities = City::model()->findAll($criteria);

                    if ($cities)
                    {
                        foreach ($cities as $city)
                        {
                            $items[] = array(
                                'id' => $city->primaryKey,
                                'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                                'value' => self::parseTemplate('{localRu}', $city),
                            );
                            $cityIds[$city->id] = $city->id;
                        }
                    }
                    $currentLimit -= count($items);
                }
            }
            Yii::app()->cache->set('autocompleteCityForHotel' . $query, $items, appParams('autocompleteCacheTime'));
        }
        return $items;
    }

    static public function getCitiesWithAirportsAndHotels($query)
    {
        $items =self::getCitiesWithAirports($query);
        if (sizeof($items)<appParams('autocompleteLimit'))
        {
            $hotels = self::getCitiesWithHotels($query);
            $j=0;
            for ($i=sizeof($items); $i<=appParams('autocompleteLimit') and ($i<sizeof($hotels)); $i++)
                $items[] = $hotels[$j++];
        }
        return $items;
    }

    static public function getCitiesWithHotelsAndAirports($query)
    {
        $items =self::getCitiesWithHotels($query);
        if (sizeof($items)<appParams('autocompleteLimit'))
        {
            $hotels = self::getCitiesWithAirports($query);
            $j=0;
            for ($i=sizeof($items); $i<=appParams('autocompleteLimit') and ($i<sizeof($hotels)); $i++)
                $items[] = $hotels[$j++];
        }
        return $items;
    }

    static public function getCities($query)
    {
        $currentLimit = appParams('autocompleteLimit');
        $items = array();//Yii::app()->cache->get('autocompleteCities' . $query);
        if (!$items)
        {
            $items = array();
            $cityIds = array();

            if (strlen($query) == 3)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                $criteria->with = 'country';
                $criteria->params[':code'] = $query;
                $criteria->addCondition('t.code = :code');
                /** @var  City[] $cities  */
                $cities = City::model()->findAll($criteria);
                if ($cities)
                {
                    foreach ($cities as $city)
                    {
                        $items[] = array(
                            'id' => $city->primaryKey,
                            'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                            'value' => self::parseTemplate('{localRu}', $city),
                        );
                        $cityIds[$city->id] = $city->id;
                    }
                }
                $currentLimit -= count($items);
            }
            $criteria = new CDbCriteria();
            $criteria->limit = $currentLimit;
            $criteria->params[':localRu'] = $query . '%';
            $criteria->params[':localEn'] = $query . '%';

            $criteria->addCondition('t.localRu LIKE :localRu OR t.localEn LIKE :localEn');
            if ($cityIds)
            {
                $criteria->addCondition('t.id NOT IN (' . join(',', $cityIds) . ')');
            }
            $criteria->with = 'country';
            $criteria->order = 'country.position desc, t.position desc';
            $cities = City::model()->findAll($criteria);
            if ($cities)
            {
                foreach ($cities as $city)
                {
                    $items[] = array(
                        'id' => $city->primaryKey,
                        'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                        'value' => self::parseTemplate('{localRu}', $city),
                    );
                    $cityIds[$city->id] = $city->id;
                }
            }
            $currentLimit -= count($items);
            if ($currentLimit)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                if (UtilsHelper::countRussianCharacters($query) > 0)
                {
                    $nameRu = $query;
                }
                else
                {
                    $nameRu = UtilsHelper::cityNameToRus($query);
                }
                $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);

                if ($metaphoneRu)
                {
                    $criteria->params[':metaphoneRu'] = $metaphoneRu;

                    $criteria->addCondition('t.metaphoneRu = :metaphoneRu');
                    if ($cityIds)
                    {
                        $criteria->addCondition('t.id NOT IN (' . join(',', $cityIds) . ')');
                    }
                    $criteria->with = 'country';
                    $criteria->order = 'country.position desc, t.position desc';
                    $cities = City::model()->findAll($criteria);

                    if ($cities)
                    {
                        foreach ($cities as $city)
                        {
                            $items[] = array(
                                'id' => $city->primaryKey,
                                'label' => self::parseTemplate('{localRu}, {country.localRu}, {code}', $city),
                                'value' => self::parseTemplate('{localRu}', $city),
                            );
                            $cityIds[$city->id] = $city->id;
                        }
                    }
                    $currentLimit -= count($items);
                }
            }
            Yii::app()->cache->set('autocompleteCities' . $query, $items, appParams('autocompleteCacheTime'));
        }
        return $items;
    }

    /**
     * Parses a template and replaces the list of attribute names with their values.
     * If the given template is null, a list of attribute names from $this->attributes will
     * be used instead.
     * @param string $template The template to use
     * @param Traversable $model The model that contains the
     * @return string The template with attribute names replaced by their values
     */
    static protected function parseTemplate($template, Traversable $model)
    {
        $replacements = array();
        preg_match_all('|\{(.+?)\}|is', $template, $matches);
        foreach ($matches[1] as $property)
        {
            if (strpos($property, '.'))
            {
                $path = explode('.', $property);
                $replacements['{' . $property . '}'] = $model->{$path[0]}->{$path[1]};
            }
            else
            {
                $replacements['{' . $property . '}'] = $model->{$property};
            }
        }
        return strtr($template, $replacements);
    }

}
