<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 19.06.12
 * Time: 18:34
 */
class BookerController extends Controller
{
    public $flightBooker;

    public function actionFlight()
    {
        if (!$this->loadModel())
        {
            $orderFlightVoyage = OrderFlightVoyage::model()->findByPk(21);
            $flightVoyage = unserialize($orderFlightVoyage->object);
            Yii::app()->flightBooker->book($flightVoyage);
            $this->run('Status');
        }
        else
        {
            $action = explode('/', $this->flightBooker->status);
            $action = $action[1];
            //$this->run($action);
            $this->run('Status');
        }
    }

    public function actionEnterCredentials()
    {

    }

    public function actionStatus($status=0)
    {
        $this->loadModel();
        if ($this and is_string($status))
        {
            $this->flightBooker->status = $status;
            $this->flightBooker->save();
        }
        echo 'You current status now: '.$this->flightBooker->swGetStatus();
        echo '<br>Your booker id = '.Yii::app()->user->getState('flightBookerId');
        echo '<br><br>';
        $items = SWHelper::nextStatuslistData($this->flightBooker, false);
        if (count($items)>0)
        {
            echo 'Next possible statuses:';
            echo '<ul>';
        }
        foreach ($items as $item=>$name)
        {
            echo '<li>'.CHtml::link($name, array('booker/status','status'=>$name)).'</li>';
        }
        if (count($items)>0)
            echo '</ul>';
        if ($this->flightBooker->swIsFinalStatus())
        {
            echo 'It is final state<br>';
            Yii::app()->user->setState('flightBookerId', null);
            echo CHtml::link('Start again', array('booker/flight'));
        }
    }

    private function loadModel()
    {
        if ($this->flightBooker==null)
        {
            $id = Yii::app()->user->getState('flightBookerId');
            $this->flightBooker = FlightBooker::model()->findByPk($id);
        }
        return $this->flightBooker;
    }
}
