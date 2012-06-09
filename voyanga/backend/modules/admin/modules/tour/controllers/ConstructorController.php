<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:41
 */
class ConstructorController extends ABaseAdminController
{
    public function actionNew()
    {
        $flightForm = new FlightForm;

        $this->render('new', array('flightForm'=>$flightForm));
    }
}
