<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 11:35
 */
class SharedMemory extends Component
{
    public $maxSize = 2097152; //2mb

    public $debug = true;

    private $fileName;

    private $shmId=0;

    private $offsetWrite = 0;

    private $offsetRead = 0;

    private $startData = 0;

    public function init()
    {
        $project = chr(getmypid() % 26 + 65);

        $dir = Yii::getPathOfAlias('application.runtime.cache');

        if (!is_dir($dir))
            mkdir($dir);

        $this->fileName = $dir.DIRECTORY_SEPARATOR.'cache_'.$project.".dump";
        try
        {
            $shmKey = ftok(__FILE__, $project);
            if ($shmKey<0)
                throw new CException('Bad ftok');
            $this->shmId = @shmop_open($shmKey, "c", 0644, $this->maxSize);
            if ($this->shmId==0)
                throw new CException('Bad shmop');

            $try = @unserialize(shmop_read($this->shmId, 0, 0));
            $this->offsetWrite = (int)$try;
            if ($this->offsetWrite == 0)
                $this->saveOffsetWrite(true);
            else
                $this->detectStart();
        }
        catch (Exception $e)
        {
            Yii::log('Unable to init shared memory', CLogger::LEVEL_ERROR, 'sharedMemory');
        }
    }

    private function detectStart()
    {
        $len = strlen((string)$this->maxSize);
        $string = sprintf('%0'.$len.'u', $this->offsetWrite);
        $this->startData = strlen(serialize($string));
    }

    public function erase()
    {
        if ($this->shmId==0)
        {
            Yii::log('Could not erase shmop', CLogger::LEVEL_ERROR, 'sharedMemory');
            return;
        }
        $date = str_repeat(" ", $this->maxSize);
        $bytes = shmop_write($this->shmId, $date, $this->maxSize);
        $this->offsetWrite == 0;
        $this->saveOffsetWrite(true);
    }

    private function saveOffsetWrite($isNew=false)
    {
        if ($this->shmId==0)
        {
            Yii::log('Could not saveOffsetWrite shmop', CLogger::LEVEL_ERROR, 'sharedMemory');
            return;
        }
        $len = strlen((string)$this->maxSize);
        $string = sprintf('%0'.$len.'u', $this->offsetWrite);
        $bytes = shmop_write($this->shmId, serialize($string), 0);
        if ($isNew)
        {
            $this->offsetWrite = $bytes;
            $this->offsetRead = $bytes;
            $this->startData = $bytes;
        }
    }

    private function strToNts($value)
    {
        return "$value##";
    }

    function strFromMem(&$value)
    {
        $i = strpos($value, "##");
        if ($i === false)
        {
            return $value;
        }
        $result =  substr($value, 0, $i);
        return $result;
    }

    public function write($obj)
    {
        if ($this->shmId==0)
        {
            Yii::log('Could not write shmop', CLogger::LEVEL_ERROR, 'sharedMemory');
            return;
        }
        Yii::log('Saving to shared memory', CLogger::LEVEL_INFO, 'sharedMemory');
        $string = serialize($obj);
        $nextOffset = strlen($string)+$this->offsetWrite;
        if ($nextOffset >= $this->maxSize)
            $this->flushToFile();
        $writtenBytes = shmop_write($this->shmId, $this->strToNts($string), $this->offsetWrite);
        $this->offsetWrite += $writtenBytes;
        $this->saveOffsetWrite();
    }

    public function flushToFile()
    {
        try
        {
            $file = fopen($this->fileName, 'a');
            $size = $this->offsetWrite - $this->startData;
            $value = shmop_read($this->shmId, $this->startData, $size);
            fwrite($file, $value);
            fclose($file);
            chmod($this->fileName, 0777);
            $this->erase();
        }
        catch (Exception $e)
        {
            Yii::log('Could not flushToFile shmop', CLogger::LEVEL_ERROR, 'sharedMemory');
        }
    }

    public function read($serialized=false)
    {
        if ($this->shmId==0)
        {
            Yii::log('Could not read shmop', CLogger::LEVEL_ERROR, 'sharedMemory');
            return;
        }
        if ($this->offsetRead>=$this->offsetWrite)
            return false;
        $value = shmop_read($this->shmId, $this->offsetRead, $this->maxSize-$this->offsetRead);
        $string = $this->strFromMem($value);
        $this->offsetRead += strlen($string) + 1;
        if ($serialized)
            return $string;
        $obj = unserialize($string);
        return $obj;
    }

    public function __destruct()
    {
        if ($this->shmId==0)
        {
            Yii::log('Could not __destruct shmop', CLogger::LEVEL_ERROR, 'sharedMemory');
            return;
        }
        shmop_close($this->shmId);
    }
}
