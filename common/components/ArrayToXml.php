<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.08.12
 * Time: 12:34
 */
class ArrayToXml
{
    private $writer;
    private $version = '1.0';
    private $encoding = 'UTF-8';
    private $rootName = 'data';

    function __construct($rootName = 'data')
    {
        $this->rootName = $rootName;
        $this->writer = new XMLWriter();
    }

    public function toXml($data)
    {
        $this->writer->openMemory();
        $this->writer->startDocument($this->version, $this->encoding);
        $this->writer->startElement($this->rootName);
        if (is_array($data))
        {
            $this->getXML($data, $this->rootName);
        }
        $this->writer->endElement();
        return $this->writer->outputMemory();
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    public function setRootName($rootName)
    {
        $this->rootName = $rootName;
    }

    private function getXML($data, $rootName)
    {
        foreach ($data as $key => $val)
        {
            if (is_numeric($key))
            {
                if (substr($rootName, -1)=='s')
                    $key = substr($rootName, 0, -1);
                else
                    $key = 'key' . $key;
            }
            if (is_array($val))
            {
                $this->writer->startElement($key);
                $this->getXML($val, $key);
                $this->writer->endElement();
            }
            else
            {
                $this->writer->writeElement($key, $val);
            }
        }
    }
}