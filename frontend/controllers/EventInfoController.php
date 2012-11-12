<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.11.12
 * Time: 16:14
 * To change this template use File | Settings | File Templates.
 */
class EventInfoController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('info');
    }

    public function actionInfo($eventId)
    {
        $event = Event::model()->findByPk($eventId);

        $this->layout = 'static';

        $this->render('info',array('event'=>$event));
    }
}
