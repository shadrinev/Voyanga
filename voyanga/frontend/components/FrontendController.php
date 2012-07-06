<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 05.07.12
 * Time: 13:26
 */
class FrontendController extends Controller
{
    public $allTabs = array(
        'avia'=>array(
            'module'=>'booking',
            'controller'=>'Flight',
            'action'=>'index'
        ),
        'hotel'=>array(
            'module'=>'booking',
            'controller'=>'Hotel',
            'action'=>'index'
        ),
        'tour'=>array(
            'module'=>'tour',
            'controller'=>'Constructor',
            'action'=>'new'
        ),
        'other'=>array()
    );

    public $output;

    public $tab = 'other';

    public function fillTabs()
    {
        foreach ($this->allTabs as $tabName => $tabInfo)
        {
            if ($tabName==$this->tab)
                continue;
            if (!isset($tabInfo['module']))
            {
                $this->output[$tabName] = '';
                continue;
            }
            ob_start();
            $moduleName = $tabInfo['module'];
            $controllerName = $tabInfo['controller'];
            $actionName = $tabInfo['action'];
            $controller = Yii::app()->createController($moduleName.'/'.$controllerName);
            $action = $controller[0]->createAction($actionName);
            $action->runWithParams(array('isTab'=>true));
            $this->output[$tabName] = ob_get_contents();
            ob_end_clean();
        }
    }

    public function render($view,$data=null,$return=false)
    {
        if (!$return)
            $this->fillTabs();
        if($this->beforeRender($view))
        {
            $this->output[$this->tab]=$this->renderPartial($view,$data,true);
            if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
                $output=$this->renderFile($layoutFile,array('content'=>$this->output,'active'=>$this->tab),true);

            $this->afterRender($view,$output);

            $this->output=$this->processOutput($output);

            if($return)
                return $this->output;
            else
                echo $this->output;
        }
    }

}
