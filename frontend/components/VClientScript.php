<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mihan007
 * Date: 23.01.13
 * Time: 14:44
 */

Yii::import('common.extensions.yii-newrelic.YiiNewRelicClientScript');

class VClientScript extends YiiNewRelicClientScript
{
    public $buildingMode = false;
    public $excludeFiles = array('runApp.js');

    public function renderHead(&$output)
    {
        if (!$this->buildingMode)
           return parent::renderHead($output);
        $root = Yii::getPathOfAlias('webroot');
        $filePath = $root.'/../../Makefile';
        $suffixFilePath = $root.'/../../suffix.php';
        $oldsuffixFilePath = $root.'/suffix.php';
        $fullPaths = array();
        $cssPaths = array();
        foreach ($this->scriptFiles[0] as $from => $to)
        {
            $add = true;
            foreach ($this->excludeFiles as $exclude)
            {
                if (endsWith($to, $exclude))
                {
                    $add = false;
                    break;
                }
            }
            if ($add)
                $fullPaths[] = $root . $to;
        }
        foreach ($this->cssFiles as $path => $media)
        {
            $cssPaths[] = $root . $path;
        }
        $makefileTemplate = file_get_contents($root.'/../../Makefile.template');
        $makefile = str_replace('{{jsFiles}}', implode(" \\\n", $fullPaths), $makefileTemplate);
        $makefile = str_replace('{{cssFiles}}', implode(" \\\n", $cssPaths), $makefile);
        if (is_file($oldsuffixFilePath))
            $oldsuffix = require_once($oldsuffixFilePath);
        else
            $oldsuffix = '';
        $suffix = '-'.time();
        $suffixFileContent = '<?php return "'.$suffix.'";';
        $makefile = str_replace('{{suffix}}', $suffix, $makefile);
        $makefile = str_replace('{{oldsuffix}}', $oldsuffix, $makefile);
        file_put_contents($filePath, $makefile);
        file_put_contents($suffixFilePath, $suffixFileContent);
        parent::renderHead($output);
    }
}
