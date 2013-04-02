<?php

//класс для работы с конфигами. потихоньку сюда всё перетащим - особенно там, где нужна логика выбора в зависимости от внешних условий
class ConfigManager
{
    static public function getNemoApiAgencyId()
    {
        if ($partner = Partner::getCurrentPartner())
        {
            $requestFromPartner = Yii::app()->user->getState('directRequest') == false;
            if ($requestFromPartner)
            {
                if (strlen(trim($partner->clientId)) > 0)
                    return trim($partner->clientId);
            }
            else
            {
                return Yii::app()->params['GDSNemo']['partnerAndGalileoAgencyId'];
            }
        }
        return Yii::app()->params['GDSNemo']['agencyId'];
    }

    static public function getNemoApiKey()
    {
        if ($partner = Partner::getCurrentPartner())
        {
            $requestFromPartner = Yii::app()->user->getState('directRequest') == false;
            if ($requestFromPartner)
            {

                if (strlen(trim($partner->apiKey)) > 0)
                    return trim($partner->apiKey);
            }
            else
            {
                return Yii::app()->params['GDSNemo']['partnerAndGalileoAgencyApiKey'];
            }
        }

        return Yii::app()->params['GDSNemo']['agencyApiKey'];
    }
}