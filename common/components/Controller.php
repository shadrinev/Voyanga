<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout='//layouts/main';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs=array();

    public function __construct($id, $module = null)
    {
        Yii::app()->configManager->configMe($this);
        parent::__construct($id, $module);
    }

    public function render($view,$data=null,$return=false)
    {
        return parent::render($view, $data, $return);
    }

    public function renderPdf($view,$data = null){
        $pdfPath = Yii::getPathOfAlias('pdfOutDir');
        $converterPath = Yii::app()->params['pdfConverterPath'];
        $htmlText = $this->renderPartial($view,$data,true);
        $htmlFileName = $view.'_'.substr(md5(uniqid('',true)),0,6);
        $isWritten = file_put_contents($pdfPath.'/'.$htmlFileName.'.html',$htmlText);
        if($isWritten){
            exec($converterPath.' '.$pdfPath.'/'.$htmlFileName.'.html '.$pdfPath.'/'.$htmlFileName.'.pdf');
            if(file_exists($pdfPath.'/'.$htmlFileName.'.pdf')){
                unlink($pdfPath.'/'.$htmlFileName.'.html');
                return $pdfPath.'/'.$htmlFileName.'.pdf';
            }
        }else{
            return false;
        }
    }
}
