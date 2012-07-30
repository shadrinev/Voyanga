<?php
/**
 * Route logs into stderr
 */
class StdErrRoute extends CLogRoute
{
    public function processLogs($logs)
    {
        foreach($logs as $log)
        {
            $message = $this->formatLogMessage($log[0], $log[1], $log[2], $log[3]);
            fwrite(STDERR, $message);
        }
    }
}