<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:06
 */
class ShowEventTripAction extends CAction
{
    private $tabs;
    private $tripElements;
    private $firstIndex;

    public function init()
    {
        $dataProvider = new TripDataProvider();
        $this->tripElements = $dataProvider->getSortedCartItems();
        $this->fillTabs();
    }

    public function run()
    {
        /** @var TourBuilderForm $model  */
        $model = Yii::app()->user->getState('tourForm');
        $cities = Yii::app()->user->getState('startCities');
        $citiesIndex = Yii::app()->user->getState('startCitiesIndex', 0);
        if (sizeof($cities)<=$citiesIndex)
        {
            $url = $this->getController()->createUrl('/admin/events/event/view', array('id'=>$model->eventId));
            Yii::app()->user->setState('tourForm', null);
            Yii::app()->user->setState('startCities', null);
            Yii::app()->user->setState('startCitiesIndex', null);
            Yii::app()->user->setState('currentCity', null);
            $this->getController()->redirect($url);
        }
        else
        {
            $city = $cities[$citiesIndex];
            $model->startCityId = $city->id;
            Yii::app()->user->setState('startCities', $model->startCities);
            Yii::app()->user->setState('startCitiesIndex', $citiesIndex+1);
            Yii::app()->user->setState('currentCity', $model->startCityId);
            Yii::app()->user->setState('tourForm', $model);
            ConstructorBuilder::buildAndPutToCart($model);
            $this->init();
            Yii::app()->getClientScript()->registerScriptFile('/js/constructorViewer.js');
            $this->controller->render('showTripForEvent', array('tabs'=>$this->tabs));
        }
    }

    private function fillTabs()
    {
        $this->tabs = array();
        foreach ($this->tripElements as $item)
        {
            $this->addNewTabOrAddElementsToExistingTabAndSetFirstTabIndex($item);
        }
        $this->setFirstTabActive();
    }

    private function addNewTabOrAddElementsToExistingTabAndSetFirstTabIndex($item)
    {
        $preparedForFrontendItem = $item->prepareForFrontend();
        if ($groupId = $item->getGroupId())
        {
            if (!isset($this->tabs[$groupId]))
            {
                $this->tabs[$groupId] = array();
                if (!$this->firstIndex)
                    $this->firstIndex = $groupId;
                $previous = array();
            }
            else
                $previous = $this->tabs[$groupId];
            $this->tabs[$groupId] = CMap::mergeArray($this->tabs[$groupId], $item->addGroupedInfo($preparedForFrontendItem));
            $this->tabs[$groupId] = $item->buildTabLabel($this->tabs[$groupId], $previous);
        }
        else
        {
            $this->tabs[] = $preparedForFrontendItem;
            if (!$this->firstIndex)
                $this->firstIndex = 0;
        }
    }

    private function setFirstTabActive()
    {
        if ($this->firstIndex)
            $this->tabs[$this->firstIndex]['active'] = true;
    }
}
