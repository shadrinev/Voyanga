<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:06
 */
class ShowTripAction extends CAction
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
        $this->init();
        Yii::app()->getClientScript()->registerScriptFile('/js/constructorViewer.js');
        $this->controller->render('showTrip', array('tabs'=>$this->tabs));
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
