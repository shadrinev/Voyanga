<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.07.12
 * Time: 10:58
 * To change this template use File | Settings | File Templates.
 */
class AjaxController extends BaseAjaxController
{

    public function actionCityForFlight($query)
    {
        $currentLimit = appParams('autocompleteLimit');
        $items = Yii::app()->cache->get('autocompleteCityForFlight'.$query);
        if(!$items)
        {
            $items = array();
            $cityIds = array();

            if(strlen($query) == 3)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                $criteria->params[':code'] = $query;
                $criteria->addCondition('t.code = :code');
                $criteria->addCondition('t.countAirports > 0');
                $criteria->with = 'country';
                /** @var  City[] $cities  */
                $cities = City::model()->findAll($criteria);

                if($cities)
                {
                    foreach($cities as $city)
                    {
                        $items[] = array(
                            'id'=>$city->primaryKey,
                            'label'=>$this->parseTemplate('{localRu}, {country.localRu}, {code}',$city),
                            'value'=>$this->parseTemplate('{localRu}',$city),
                        );
                        $cityIds[$city->id] = $city->id;
                    }
                }
                $currentLimit -= count($items);
            }
            $criteria = new CDbCriteria();
            $criteria->limit = $currentLimit;
            $criteria->params[':localRu'] = $query.'%';
            $criteria->params[':localEn'] = $query.'%';

            $criteria->addCondition('t.localRu LIKE :localRu OR t.localEn LIKE :localEn');
            $criteria->addCondition('t.countAirports > 0');
            if($cityIds)
            {
                $criteria->addCondition('t.id NOT IN ('.join(',',$cityIds).')');
            }
            $criteria->with = 'country';
            $criteria->order = 'country.position desc, t.position desc';
            $cities = City::model()->findAll($criteria);

            if($cities)
            {
                foreach($cities as $city)
                {
                    $items[] = array(
                        'id'=>$city->primaryKey,
                        'label'=>$this->parseTemplate('{localRu}, {country.localRu}, {code}',$city),
                        'value'=>$this->parseTemplate('{localRu}',$city),
                    );
                    $cityIds[$city->id] = $city->id;
                }
            }
            $currentLimit -= count($items);
            if($currentLimit)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                if(UtilsHelper::countRussianCharacters($query) > 0)
                {
                    $nameRu = $query;
                }else{

                    $nameRu = UtilsHelper::cityNameToRus($query);
                }
                $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);

                if($metaphoneRu)
                {
                    $criteria->params[':metaphoneRu'] = $metaphoneRu;

                    $criteria->addCondition('t.metaphoneRu = :metaphoneRu');
                    $criteria->addCondition('t.countAirports > 0');
                    if($cityIds)
                    {
                        $criteria->addCondition('t.id NOT IN ('.join(',',$cityIds).')');
                    }
                    $criteria->with = 'country';
                    $criteria->order = 'country.position desc, t.position desc';
                    $cities = City::model()->findAll($criteria);

                    if($cities)
                    {
                        foreach($cities as $city)
                        {
                            $items[] = array(
                                'id'=>$city->primaryKey,
                                'label'=>$this->parseTemplate('{localRu}, {country.localRu}, {code}',$city),
                                'value'=>$this->parseTemplate('{localRu}',$city),
                            );
                            $cityIds[$city->id] = $city->id;
                        }
                    }
                    $currentLimit -= count($items);
                }
            }
            Yii::app()->cache->set('autocompleteCityForFlight'.$query,$items,appParams('autocompleteCacheTime'));
        }
        //VarDumper::dump($items);
        $this->send($items);
    }

    public function actionCityForHotel($query)
    {
        $currentLimit = appParams('autocompleteLimit');
        $items = Yii::app()->cache->get('autocompleteCityForHotel'.$query);
        if(!$items)
        {
            $items = array();
            $cityIds = array();

            if(strlen($query) == 3)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                $criteria->params[':code'] = $query;
                $criteria->addCondition('t.code = :code');
                $criteria->addCondition('t.hotelbookId > 0');
                $criteria->with = 'country';
                /** @var  City[] $cities  */
                $cities = City::model()->findAll($criteria);

                if($cities)
                {
                    foreach($cities as $city)
                    {
                        $items[] = array(
                            'id'=>$city->primaryKey,
                            'label'=>$this->parseTemplate('{localRu}, {country.localRu}, {code}',$city),
                            'value'=>$this->parseTemplate('{localRu}',$city),
                        );
                        $cityIds[$city->id] = $city->id;
                    }
                }
                $currentLimit -= count($items);
            }
            $criteria = new CDbCriteria();
            $criteria->limit = $currentLimit;
            $criteria->params[':localRu'] = $query.'%';
            $criteria->params[':localEn'] = $query.'%';

            $criteria->addCondition('t.localRu LIKE :localRu OR t.localEn LIKE :localEn');
            $criteria->addCondition('t.hotelbookId > 0');
            if($cityIds)
            {
                $criteria->addCondition('t.id NOT IN ('.join(',',$cityIds).')');
            }
            $criteria->with = 'country';
            $criteria->order = 'country.position desc, t.position desc';
            $cities = City::model()->findAll($criteria);

            if($cities)
            {
                foreach($cities as $city)
                {
                    $items[] = array(
                        'id'=>$city->primaryKey,
                        'label'=>$this->parseTemplate('{localRu}, {country.localRu}, {code}',$city),
                        'value'=>$this->parseTemplate('{localRu}',$city),
                    );
                    $cityIds[$city->id] = $city->id;
                }
            }
            $currentLimit -= count($items);
            if($currentLimit)
            {
                $criteria = new CDbCriteria();
                $criteria->limit = $currentLimit;
                if(UtilsHelper::countRussianCharacters($query) > 0)
                {
                    $nameRu = $query;
                }else{

                    $nameRu = UtilsHelper::cityNameToRus($query);
                }
                $metaphoneRu = UtilsHelper::ruMetaphone($nameRu);

                if($metaphoneRu)
                {
                    $criteria->params[':metaphoneRu'] = $metaphoneRu;

                    $criteria->addCondition('t.metaphoneRu = :metaphoneRu');
                    $criteria->addCondition('t.hotelbookId > 0');
                    if($cityIds)
                    {
                        $criteria->addCondition('t.id NOT IN ('.join(',',$cityIds).')');
                    }
                    $criteria->with = 'country';
                    $criteria->order = 'country.position desc, t.position desc';
                    $cities = City::model()->findAll($criteria);

                    if($cities)
                    {
                        foreach($cities as $city)
                        {
                            $items[] = array(
                                'id'=>$city->primaryKey,
                                'label'=>$this->parseTemplate('{localRu}, {country.localRu}, {code}',$city),
                                'value'=>$this->parseTemplate('{localRu}',$city),
                            );
                            $cityIds[$city->id] = $city->id;
                        }
                    }
                    $currentLimit -= count($items);
                }
            }
            Yii::app()->cache->set('autocompleteCityForHotel'.$query,$items,appParams('autocompleteCacheTime'));
        }
        //VarDumper::dump($items);
        $this->send($items);
    }
}
