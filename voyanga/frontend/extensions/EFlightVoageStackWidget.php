<?php
//todo: move to widgets
class EFlightVoageStackWidget extends CWidget
{
    public $FlightVoyageStrategy;
    public $data;
    public $viewName;

    public function init()
    {

    }

    public function run()
    {
        //echo "run";
        if (!$this->viewName)
        {
            $this->viewName = 'kg_am';
        }
        $this->render($this->viewName, array('flightSearchKey' => $this->flightSearchKey));
    }
}