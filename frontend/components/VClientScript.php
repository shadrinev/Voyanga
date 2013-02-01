<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mihan007
 * Date: 23.01.13
 * Time: 14:44
 */
class VClientScript extends CClientScript
{
    public $buildingMode = false;

    public function renderHead(&$output)
    {
        if (!$this->buildingMode)
           return parent::renderHead($output);
        $root = Yii::getPathOfAlias('webroot');
        $filePath = $root.'/../../Makefile';
        $fullPaths = array();
        $cssPaths = array();
        foreach ($this->scriptFiles[0] as $from => $to)
        {
            $fullPaths[] = $root . $to;
        }
        foreach ($this->cssFiles as $path => $media)
        {
            $cssPaths[] = $root . $path;
        }
        $makefileTemplate = file_get_contents($root.'/../../Makefile.template');
        $makefile = str_replace('{{jsFiles}}', implode(" \\\n", $fullPaths), $makefileTemplate);
        $makefile = str_replace('{{cssFiles}}', implode(" \\\n", $cssPaths), $makefile);
        file_put_contents($filePath, $makefile);
        parent::renderHead($output);
    }
}
