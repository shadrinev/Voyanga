<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 10.07.12
 * Time: 13:12
 * To change this template use File | Settings | File Templates.
 */
class FlightBookingCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
USAGE FlightBooking command [OPTIONS]
   ...
Options:
--type=(value) - Default value airports
   ...
EOD;
    }

    /**
     * Execute the action.
     * @param array command line parameters specific for this command
     */
    public function actionIndex($type = 'changeState', $flightBookerId = 0, $newState = '')
    {
        if($type == 'changeState' )
        {
            if($flightBookerId)
            {
                /** @var FlightBookerComponent $flightBookerComponent  */
                $flightBookerComponent = Yii::app()->flightBooker;
                $flightBookerComponent->setFlightBookerFromId($flightBookerId);
                if($newState)
                {
                    $flightBookerComponent->status($newState);
                }
            }
            else
            {
                echo $this->getHelp();
            }
        }
        else
        {
            echo $this->getHelp();
        }
    }

    public function actionChangeState($flightBookerId = 0, $newState = '')
    {

        if($flightBookerId)
        {
            /** @var FlightBookerComponent $flightBookerComponent  */
            $flightBookerComponent = Yii::app()->flightBooker;
            $flightBookerComponent->setFlightBookerFromId($flightBookerId);
            echo "FlightBookerId ".$flightBookerComponent->getFlightBookerId()."\n";

            if($newState)
            {
                $flightBookerComponent->status($newState);
                echo "changed\n";
            }
        }
        else
        {
            $helpText = $this->getHelp();
            $helpText = str_replace('command', 'ChangeState',$helpText);
            echo $helpText;
        }
    }
}
