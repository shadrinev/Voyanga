<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 06.09.12 11:41
 */
class EventController extends BaseAjaxController
{
    public function actionAdd()
    {
        $event = new Event();
        $event->scenario = 'frontend';
        $event->attributes = $_POST;
        if ($event->save())
            $this->send(array('id'=>$event->id, 'message'=>'Создано новое событие с именем '.$event->title));
        else
        {
            $this->sendError(500, 'Error while saving event');
        }
    }

    public function actionGetAllEvents()
    {
        $events = Event::model()->findAll(array('select'=>array('id','title','startDate')));
        $response = array();
        foreach ($events as $event)
        {
            $element = array();
            $element['id'] = $event->id;
            $element['title'] = $event->title;
            $element['startDate'] = DateTimeHelper::formatForEventForm($event->startDate);
            $response[] = $element;
        }
        $this->send($response);
    }
}
