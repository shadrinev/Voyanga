<?php
/**
 * GDSAdapter class
 * Frontend layer GDS adapter
 * @author oleg
 *
 */
class GDSAdapter extends CApplicationComponent
{
    public function flightSearch($params)
    {
        $data = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/flightsearch.json');
        $status = 'ok';
        $description = '';
        if ($data)
        {
            $jData = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/flightsearch.json'));
            if (!$jData->section)
            {
                $status = 'error';
                $description = 'Error input parameters';
            }
        } else
        {
            $status = 'error';
            $description = 'Cant connect to remote GDS Adapter';
        }
        if ($status == 'error')
        {
            throw new CException(Yii::t('application', 'Problem in FlightSearch request. Reason: {description}', array(
                '{description}' => $description)));
            return FALSE;
        } else
            return $jData;
    }

    public function flightBooking()
    {

    }

    public function flightTariffRules()
    {

    }

    public function flightTicketing()
    {

    }

    public function flightVoid()
    {

    }
}