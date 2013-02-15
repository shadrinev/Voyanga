<?php
    class VoyangaLogFilter extends CLogFilter
    {

        public function filter(&$logs)
        {

            $mylogs = array();
            foreach ($logs as $log)
            {
                  if (strpos($log[2], 'exception.CHttpException') === false)
                      $mylogs[] = $log;

            }
            $logs = $mylogs;
            parent::filter($logs);
        }
    }
?>