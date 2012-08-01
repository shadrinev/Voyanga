<?php Yii::import('site.common.modules.tour.models.*'); ?>
<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 13.06.12
 * Time: 19:06
 */
class BackendViewerController extends Controller
{
    public function actions()
    {
        return array(
            'view' => array('class'=>'site.common.modules.tour.actions.viewer.ViewAction'),
            'index' => array('class'=>'site.common.modules.tour.actions.viewer.IndexAction'),
        );
    }
}
