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
        return array('flightBookerBehavior' => array(
            'class' => 'common.components.flightBooker.FlightBookerBehavior'
        ));
    }

    public function actionStatus($status = 0)
    {
        $this->loadModel();
        if ($this and is_string($status))
        {
            $this->flightBooker->status = $status;
            $this->flightBooker->save();
        }
        echo 'You current status now: ' . $this->flightBooker->swGetStatus();
        echo '<br>Your booker id = ' . Yii::app()->user->getState('flightBookerId');
        echo '<br><br>';
        $items = SWHelper::nextStatuslistData($this->flightBooker, false);
        if (count($items) > 0)
        {
            echo 'Next possible statuses:';
            echo '<ul>';
        }
        foreach ($items as $item => $name)
        {
            echo '<li>' . CHtml::link($name, array('booker/status', 'status' => $name)) . '</li>';
        }
        if (count($items) > 0)
            echo '</ul>';
        if ($this->flightBooker->swIsFinalStatus())
        {
            echo 'It is final state<br>';
            Yii::app()->user->setState('flightBookerId', null);
            echo CHtml::link('Start again', array('booker/flight'));
        }
    }
}
