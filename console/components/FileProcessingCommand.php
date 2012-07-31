<?php
/**
 * Abstract class, performs validate first command line argument
 * to be valid/readable path to file.
 */
abstract class FileProcessingCommand extends CConsoleCommand
{
    /**
     * Implement this function in subclass.
     *
     * @param resource $fh file handler opened for reading..
     */
    abstract public function process($fh);

    /**
     * Shortcut to Yii::log
     */
    public function logError($msg)
    {
        Yii::log($msg, 'error', 'console.' . $this->getName());
    }

    public function run($args)
    {
        if (count($args)==0)
        {
            echo $this->getHelp();
            Yii::app()->end();
        }

        if (!is_readable($args[0]))
        {
            $this->logError("Given file is not readable by current user or does not exists.");
            echo $this->getHelp();
            Yii::app()->end();
        }

        Yii::import("common.modules.hotel.models.HotelRating");

        $fh = fopen($args[0], 'r');

        if($fh===FALSE)
        {
            // Should never happen in real life
            $this->logError("File exists and readable, yet i cant open it for reading.");
            echo $this->getHelp();
            Yii::app()->end();
        }
        $this->process($fh);
        fclose($fh);
    }
}