<?php
class ShortUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager,$route,$params,$ampersand)
    {
        return false;
    }

    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
        if (strpos($pathInfo, Yii::app()->params['shortUrl.prefix']) !== 0)
            return false;
        $shortUrl = ShortUrl::model()->findByAttributes(array('short_url'=>$pathInfo));
        if ($shortUrl)
        {
            Yii::app()->getRequest()->redirect($shortUrl->full_url, true, 301);
        }
        return false;  // не применяем данное правило
    }
}