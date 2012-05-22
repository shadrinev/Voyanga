<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 17:05
 */
class SyncController extends Controller
{

    public function actionGet()
    {
        $dir = Yii::getPathOfAlias(Yii::app()->params['sharedMemory']['flushDirectory']);
        $files = CFileHelper::findFiles($dir, array('fileTypes'=>array(Yii::app()->params['sharedMemory']['flushExtension'])));
        if (isset($files[0]))
        {
            $size = filesize($files[0]);
            $file = fopen($files[0], 'r');
            $content = fread($file, $size);
            fclose($file);
            echo $content;
            //unlink($files[0]);
            Yii::app()->end();
        }
    }
}
