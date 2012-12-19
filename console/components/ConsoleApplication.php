<?php

class ConsoleApplication extends CConsoleApplication {

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
        $_SERVER['SERVER_NAME']='test.voyanga.com';
        $this->getComponent('themeManager')->setBasePath('/home/voyanga/app/frontend/www/themes/');
        return $this->getComponent('themeManager')->getTheme('v2');
    }
    public function getViewRenderer()
    {
        return $this->getComponent('viewRenderer');
    }


}