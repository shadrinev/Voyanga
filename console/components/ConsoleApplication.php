<?php

class ConsoleApplication extends CConsoleApplication {
    public $host;
    public $themePath;


    public function init() {
        parent::init();
        $_SERVER['SERVER_NAME']= $this->host;
        $_SERVER['HTTP_HOST'] = $this->host;
    }

    protected function registerCoreComponents()
    {
        parent::registerCoreComponents();

        $components=array(
            'user'=>array(
                'class'=>'ConsoleUser',
            ),
            'themeManager'=>array(
                'class'=>'CThemeManager',
            ),
        );

        $this->setComponents($components);
    }

    public function getTheme()
    {
        $this->getComponent('themeManager')->setBasePath($this->themePath);
        //$this->getComponent('themeManager')->setBasePath('/srv/www/oleg.voyanga/public_html/frontend/www/themes/');
        return $this->getComponent('themeManager')->getTheme('v2');
    }

    public function getViewRenderer()
    {
        return $this->getComponent('viewRenderer');
    }


}