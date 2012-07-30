<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 17:05
 */
class SyncController extends Controller
{
    const CHUNK_SIZE = 1e6;

    public function actionGet()
    {
        $dir = Yii::getPathOfAlias(Yii::app()->params['sharedMemory']['flushDirectory']);
        $files = CFileHelper::findFiles($dir, array('fileTypes'=>array(Yii::app()->params['sharedMemory']['flushExtension'])));
        if (isset($files[0]))
        {
            $size = filesize($files[0]);
            $file = fopen($files[0], 'r');
            $descriptor = $files[0].".descr";
            if (is_file($descriptor))
            {
                $descr = fopen($descriptor, "r");
                $pos = fread($descr, filesize($descriptor));
                fclose($descr);
            }
            else
            {
                $pos = 0;
            }
            fseek($file, $pos);
            $content = fread($file, self::CHUNK_SIZE);
            $position = strrpos($content, '##')+2;
            $pos += $position;
            echo substr($content, 0, $position);
            fclose($file);

            $descr = fopen($descriptor, "w");
            fwrite($descr, $pos);
            fclose($descr);
            /*if ($pos>=$size)
            {
                unlink($files[0]);
                unlink($descriptor);
            }*/
            Yii::app()->end();
        }
    }
}
