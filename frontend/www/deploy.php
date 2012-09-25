<?php
function _exec($cmd)
{
    $out = "$ " . $cmd . "\n";
    $out .= exec($cmd . ' 2>&1');
    _log($out);
}

function _log($str)
{
    $out = "\n" . date('r') . " " . $str;
    echo $out;
    error_log($out, 3, '.webhook.log');
}

//$github_ips = array('207.97.227.253', '50.57.128.197', '108.171.174.178');

/*if (in_array($_SERVER['REMOTE_ADDR'], $github_ips))
{*/
    $dir = '/home/voyanga/app/';
    $result = _exec("cd $dir && git pull && {$dir}yiic migrate --interactive=0");
    echo 'Done.';
/*}
else
{
    header('HTTP/1.1 404 Not Found');
    echo '404 Not Found.';
    exit;
}*/