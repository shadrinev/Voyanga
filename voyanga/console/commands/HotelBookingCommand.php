<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 09.07.12
 * Time: 11:04
 *
 * command example: HotelBooking command [OPTIONS]
 * To change this template use File | Settings | File Templates.
 */
class HotelBookingCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
USAGE HotelBooking command [OPTIONS]
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
    public function actionIndex($type = 'changeState', $hotelBookerId = 0, $newState = '')
    {
        if($type == 'changeState' )
        {
            if($hotelBookerId)
            {
                /** @var HotelBookerComponent $hotelBookerComponent  */
                $hotelBookerComponent = Yii::app()->hotelBooker;
                $hotelBookerComponent->setHotelBookerFromId($hotelBookerId);
                if($newState)
                {
                    $hotelBookerComponent->status($newState);
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

    public function actionChangeState($hotelBookerId = 0, $newState = '')
    {

        if($hotelBookerId)
        {
            /** @var HotelBookerComponent $hotelBookerComponent  */
            $hotelBookerComponent = Yii::app()->hotelBooker;
            $hotelBookerComponent->setHotelBookerFromId($hotelBookerId);
            echo "HotelBookerId ".$hotelBookerComponent->getHotelBookerId()."\n";

            if($newState)
            {
                $hotelBookerComponent->status($newState);

                echo "Status is ".$hotelBookerComponent->getStatus()."\n";
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
