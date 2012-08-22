<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 22.08.12 15:57
 */
class PhpMorphy extends CApplicationComponent
{
    public $libraryVersion = '0.3.7';
    public $options;
    public $language;

    private $morphy;

    public function init()
    {
        Yii::setPathOfAlias('phpmorphy', realpath(dirname(__FILE__)));
        $libraryPath = 'phpmorphy.vendors.phpmorphy-'.$this->libraryVersion;
        $libraryMainRequireFile = Yii::getPathOfAlias($libraryPath.'.src').DIRECTORY_SEPARATOR.'common.php';
        require_once ($libraryMainRequireFile);
        $dictionaryPath = Yii::getPathOfAlias($libraryPath.'.dicts');
        $language = Yii::app()->getLanguage();
        $this->options = CMap::mergeArray($this->options, $this->getDefaultOptions());
        try
        {
            $this->morphy = new phpMorphy($dictionaryPath, $language, $this->options);
        }
        catch (phpMorphy_Exception $e)
        {
            throw new CException('Error occured while creating phpMorphy instance: ' . $e->getMessage());
        }
    }

    public function __call($name, $params)
    {
        $this->morphy->$name($params);
    }

    private function getDefaultOptions()
    {
        return array(
            'storage' => PHPMORPHY_STORAGE_FILE,
        );
    }
}
