<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 21.05.12
 * Time: 16:34
 * To change this template use File | Settings | File Templates.
 */
class UpdateHotelbookRoomNamesCanonicalCommand extends CConsoleCommand
{



    public function getHelp()
    {
        return <<<EOD
USAGE UpdateHotelbookRoomNamesCanonical [anything]
   ...
EOD;
    }

    /**
     * Execute the action.
     * @param array command line parameters specific for this command
     */
    public function actionIndex($type = 'roomNames',$testWord = '')
    {
        if ($type == 'roomNames') {
            $connection=Yii::app()->db;
            $command=$connection->createCommand("SELECT * FROM room_names_nemo");
            echo "Memory usage: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
            $dataReader=$command->query();
            echo "Memory usage: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
            $i = 0;
            while(($row=$dataReader->read())!==false) {
                //CVarDumper::dump($row);
                /*echo "{";
                foreach($row as $key=>$val){
                    echo "$key : $val ,";
                }
                echo "}\n";*/
                $roomInfo = HotelRoom::parseRoomNameStatic($row['roomNameCanonical']);
                $sql = 'SELECT * FROM room_names_nemo WHERE ';
                $conductions = array();
                foreach($row as $key=>$val){
                    if($val){
                        $conductions[$key] = "{$key} = '{$val}'";
                    }else{
                        $conductions[$key] = "{$key} IS NULL";
                    }
                }
                $conductions['id'] = "id != {$row['id']}";
                $conductions['roomNameCanonical'] = "roomNameCanonical = '".addslashes($roomInfo['roomNameCanonical'])."'";
                unset($conductions['roomNameRusId']);
                $sql .= join(' AND ',$conductions);
                //echo "sql: {$sql}\n";

                $commandFind=$connection->createCommand($sql);

                $findRow = $commandFind->queryRow();
                if($findRow){
                    echo "{";
                    foreach($findRow as $key=>$val){
                        echo "$key : $val ,";
                    }
                    echo "}\n";
                }
                $roomName = new RoomNamesNemo();
                $roomName->setAttributes($row);
                if($findRow){
                    if($findRow['roomNameRusId']){
                        $roomName->roomNameRusId = $findRow['roomNameRusId'];
                    }
                    $count=$connection->createCommand("DELETE FROM room_names_nemo WHERE id = {$findRow['id']}")->execute();
                    if(!$count){
                        echo "Cant delete"."DELETE FROM room_names_nemo WHERE id = {$findRow['id']} \n";
                    }
                }
                unset($findRow);
                unset($commandFind);
                $roomName->roomNameCanonical = $roomInfo['roomNameCanonical'];
                $roomName->id = $row['id'];
                $roomName->setIsNewRecord(false);
                $roomName->save();
                unset($roomName);
                unset($sql);
                unset($roomInfo);
                if($i % 100 == 0){
                    echo "Memory usage: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";
                    //break;
                }
                $i++;
            }
            echo "Memory usage: {peak:" . (ceil(memory_get_peak_usage() /1024)) . "kb , now: ".(ceil(memory_get_usage() /1024))."kb }\n";


        }elseif ($type == 'testRoomNames') {
            CVarDumper::dump(HotelRoom::parseRoomNameStatic($testWord));
        }

    }
}
