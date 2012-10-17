<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 19.06.12
 * Time: 18:34
 */
class BookerController extends Controller
{
    public function actions()
    {
        return array(
            'buyFlight' => array(
                'class' => 'common.components.flightBooker.actions.Engine',
            ),
            'stageEnterCredentials' => array(
                'class' => 'common.components.flightBooker.actions.EnterCredentials',
            )
        );
    }

    public function behaviors()
    {
        //return array('flightBookerBehavior' => array(
        //    'class' => 'common.components.flightBooker.FlightBookerBehavior'
        //));
        return array();
    }

    public function actionStatus($flightBookerId,$status = 0)
    {
        //$this->loadModel();
        //die();

        /** @var FlightBookerComponent $flightBookerComponent  */
        $flightBookerComponent = Yii::app()->flightBooker;
        $flightBookerComponent->setFlightBookerFromId($flightBookerId);

        //echo "FlightBookerId ".$flightBookerComponent->getFlightBookerId()."\n";

        //if($newState)
        //{
        //    $flightBookerComponent->status($newState);
        //    echo "changed\n";
        //}

        if ($this and is_string($status))
        {
            $flightBookerComponent->getCurrent()->status = $status;
            $flightBookerComponent->getCurrent()->save();
        }
        echo 'You current status now: ' . $flightBookerComponent->getCurrent()->swGetStatus();
        echo '<br>Your booker id = ' . Yii::app()->user->getState('flightBookerId');
        echo '<br><br>';
        $items = SWHelper::nextStatuslistData($flightBookerComponent->getCurrent(), false);
        if (count($items) > 0)
        {
            echo 'Next possible statuses:';
            echo '<ul>';
        }
        foreach ($items as $item => $name)
        {
            echo '<li>' . CHtml::link($name, array('booker/status', 'status' => $name,'flightBookerId'=>$flightBookerId)) . '</li>';
        }
        if (count($items) > 0)
            echo '</ul>';
        if ($flightBookerComponent->getCurrent()->swIsFinalStatus())
        {
            echo 'It is final state<br>';
            Yii::app()->user->setState('flightBookerId', null);
            echo CHtml::link('Start again', array('booker/flight'));
        }
    }
}
