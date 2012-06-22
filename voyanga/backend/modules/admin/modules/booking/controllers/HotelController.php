<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 20.06.12
 * Time: 13:00
 */
class HotelController extends ABaseAdminController
{
    public function actionIndex()
    {
        $hotelForm = new HotelForm;
        if(isset($_POST['ajax']) && $_POST['ajax']==='hotel-form')
        {
            echo CActiveForm::validate($hotelForm);
            Yii::app()->end();
        }
        $this->render('index', array(
            'items'=>$this->generateItems(),
            'hotelForm'=>$hotelForm,
            'autosearch'=>false,
            'cityName'=>'',
            'fromDate'=>'',
            'duration'=>1
        ));
    }

    public function generateItems()
    {
        $elements = Yii::app()->user->getState('lastHotelSearches');
        if (!is_array($elements))
            return false;
        $items = array();
        foreach ($elements as $element)
        {
            $item = array(
                'label' => City::model()->getCityByPk($element[0])->localRu . ', ' . $element[1] . ' - ' . $element[2],
                'url' => '/admin/booking/hotel/search/city/'.$element[0].'/from/'.$element[1].'/to/'.$element[2],
                'encodeLabel' => false
            );
            $items[] = $item;
        }
        return $items;
    }
}
