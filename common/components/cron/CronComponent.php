<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 05.07.12
 * Time: 11:09
 */
class CronComponent extends CApplicationComponent
{
    public $executor = 'yiic';
    public $executorPath = '/srv/www/voyanga/public_html/';

    public function init()
    {
        Yii::setPathOfAlias('cron', realpath(dirname(__FILE__)));
        Yii::import('cron.*');
    }

    /**
     * @param string|integer $time timestamp or parsable through strtotime string
     * @param $command
     * @param string $action
     * @param array $params
     * @return bool|string returns jobId or false if job can't be set
     */
    public function add($time, $command, $action='cron', $params=array())
    {
        $command = $this->buildAddAtCommand($time, $command, $action, $params);
        $command .= ' 2>&1';
        $out = "Running command: ".$command."\n";
        $result = array ();
        exec ($command, $result);
        $out .= "Result is: \n";
        $resultString = '';
        foreach($result as $row)
        {
            $out .= $row."\n";
            $resultString .= $row.' ';
        }
        Yii::log($out, 'at', 'cron');
        return $this->parseResult($resultString);
    }

    public function parseResult($result)
    {
        $pattern = "!.*?job\s+(\d+)\s+?!ims";
        preg_match_all($pattern, $result, $matches);
        if (isset($matches[1]))
            if (isset($matches[1][0]))
                return $matches[1][0];
        return false;
    }

    /**
     * @param $jobId to delete
     * @return bool return true if job deleted successfully
     */
    public function delete($jobId)
    {
        $command = $this->buildDeleteAtCommand($jobId);
        $command .= ' 2>&1';
        $out = "Running command: ".$command."\n";
        $result = array ();
        exec ($command, $result);
        $out .= "Result is: \n";
        $resultString = '';
        foreach($result as $row)
        {
            $out .= $row."\n";
            $resultString .= $row.' ';
        }
        Yii::log($out, 'at', 'cron');
        return (strlen($resultString)==0);
    }

    private function buildDeleteAtCommand($jobId)
    {
        $command = "atrm ".$jobId;
        return $command;
    }

    private function buildAddAtCommand($time, $component, $action='cron', $params=array())
    {
        $paramsPrepared = $this->prepareParams($params);
        $command = $this->executorPath.$this->executor.' '.$component.' '.$action.' '.$paramsPrepared;
        if (is_numeric($time))
            $time = date('h:i A d.m.Y', $time);
        else
            $time = date('h:i A d.m.Y', strtotime($time));
        $commandAt = $command.' | at '.$time;
        return $commandAt;
    }

    private function prepareParams($params)
    {
        $output = '';
        foreach ($params as $paramName=>$paramValue)
        {
            $output .= ' --'.$paramName.'='.$paramValue;
        }
        return $output;
    }
}
