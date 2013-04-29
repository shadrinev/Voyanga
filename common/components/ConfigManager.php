<?php

//класс для работы с конфигами. потихоньку сюда всё перетащим - особенно там, где нужна логика выбора в зависимости от внешних условий
class ConfigManager
{
    static public $forcePartnerAndGalileo = false;

    static public function getNemoApiAgencyId()
    {
        if ($partner = Partner::getCurrentPartner())
        {
            $requestFromPartner = Yii::app()->user->getState('directRequest') == false;
            if ($requestFromPartner && ! ConfigManager::$forcePartnerAndGalileo)
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
            if ($requestFromPartner && ! ConfigManager::$forcePartnerAndGalileo)
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