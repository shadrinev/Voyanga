<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:31
 */
class IndexAction extends CAction
{
    public function run()
    {
        $dataProvider=new CActiveDataProvider('Order');
        $this->controller->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }
}
