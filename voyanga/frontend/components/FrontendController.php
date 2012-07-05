<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 05.07.12
 * Time: 13:26
 */
class FrontendController extends Controller
{
    public $tab = 'other';

    public function render($view,$data=null,$return=false)
    {
        if($this->beforeRender($view))
        {
            $output[$this->tab]=$this->renderPartial($view,$data,true);
            if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
                $output=$this->renderFile($layoutFile,array('content'=>$output,'active'=>$this->tab),true);

            $this->afterRender($view,$output);

            $output=$this->processOutput($output);

            if($return)
                return $output;
            else
                echo $output;
        }
    }

}
