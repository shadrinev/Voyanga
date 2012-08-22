<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 22.08.12 15:57
 */
class PhpMorphyComponent extends CApplicationComponent
{
    public $libraryVersion = '0.3.7';
    public $options;
    public $language;

    private $morphy;

    public function init()
    {
        Yii::setPathOfAlias('phpmorphy', realpath(dirname(__FILE__)));
        $libraryPath = Yii::getPathOfAlias('phpmorphy.vendors.phpmorphy-') . $this->libraryVersion;
        $libraryMainRequireFile = $libraryPath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'common.php';
        require_once ($libraryMainRequireFile);
        $dictionaryPath = $libraryPath . DIRECTORY_SEPARATOR . 'dicts' . DIRECTORY_SEPARATOR . 'utf-8';
        $language = Yii::app()->getLanguage();
        setlocale(LC_ALL, $language);
        $this->options = CMap::mergeArray($this->options, $this->getDefaultOptions());
        try
        {
            $this->morphy = new PhpMorphy($dictionaryPath, $language, $this->options);
        }
        catch (phpMorphy_Exception $e)
        {
            throw new CException('Error occured while creating phpMorphy instance: ' . $e->getMessage());
        }
    }

    public function __call($name, $params)
    {
        try
        {
            return parent::__call($name, $params);
        }
        catch (CException $e)
        {
            if (method_exists($this->morphy, $name))
                return call_user_func_array(array($this->morphy, $name), $params);
            else
                throw $e;
        }
    }

    private function getDefaultOptions()
    {
        return array(
            'storage' => PHPMORPHY_STORAGE_MEM,
            // Enable prediction by suffix
            'predict_by_suffix' => true,
            // Enable prediction by prefix
            'predict_by_db' => true,
        );
    }
}
