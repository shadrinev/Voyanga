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
        if ($type == 'changeState')
        {
            if ($hotelBookerId)
            {
                /** @var HotelBookerComponent $hotelBookerComponent  */
                $hotelBookerComponent = new HotelBookerComponent();
                $hotelBookerComponent->setHotelBookerFromId($hotelBookerId);
                if ($newState)
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
        if ($hotelBookerId)
        {
            /** @var HotelBookerComponent $hotelBookerComponent  */
            $hotelBookerComponent = New HotelBookerComponent();
            $hotelBookerComponent->setHotelBookerFromId($hotelBookerId);
            echo "HotelBookerId " . $hotelBookerComponent->getHotelBookerId() . PHP_EOL;
            echo "Current status is " . $hotelBookerComponent->getCurrent()->swGetStatus() . PHP_EOL;
            echo "Next possible status are " . $hotelBookerComponent->getCurrent()->swGetNextStatus() . PHP_EOL;
            echo "Trying to change status to $newState" . PHP_EOL;
            if ($newState)
            {
                $res = $hotelBookerComponent->status($newState);
                if (!$res)
                {
                    CVarDumper::dump($hotelBookerComponent->getCurrent()->getErrors());
                    CVarDumper::dump($hotelBookerComponent->getCurrent()->getAttributes());
                    echo PHP_EOL;
                }
                else
                    $hotelBookerComponent->getCurrent()->onlySave();
                echo "Status is " . $hotelBookerComponent->getCurrent()->swGetStatus() . "\n";
            }
        }
        else
        {
            $helpText = $this->getHelp();
            $helpText = str_replace('command', 'ChangeState', $helpText);
            echo $helpText;
        }
    }
}
