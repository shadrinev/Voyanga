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

    /**
     * Declares class-based actions.
     */
    public function actions()
    {

        return array(
            'cityAutocomplete'=>array(
                'class'=>'application.actions.AAutoCompleteAction',
                'modelClass'=>'City',
                'cache'=>false,
                'cacheExpire'=>1800,
                'attributes'=>array('localRu','localEn','code:='),
                'labelTemplate'=>'{localRu}, {country.localRu}, {code}',
                'valueTemplate'=>'{localRu}',
                'criteria'=>array('with'=>'country','condition'=>'countAirports!=0'),
                'paramName'=>'query'
            ),
            'cityAutocompleteForHotel'=>array(
                'class'=>'application.actions.AAutoCompleteAction',
                'modelClass'=>'City',
                'cache'=>false,
                'cacheExpire'=>1800,
                'attributes'=>array('localRu','localEn','code:='),
                'labelTemplate'=>'{localRu}, {country.localRu}, {code}',
                'valueTemplate'=>'{localRu}',
                'criteria'=>array('with'=>'country'),
                'paramName'=>'query'
            ),
        );
    }
}
