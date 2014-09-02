<?php

$src_dir = '/var/www/logistic/html';
$dst_file = '/tmp/lp_'.(md5(time())).'.tar.gz';

$cmd = 'tar -czf '.$dst_file.' --exclude=.svn '.$src_dir;

$return = 0;
$output = array();

exec($cmd, $output, $return);

if($return) {
    print_r($output);
    die;
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($dst_file));
header('Content-Disposition: attachment; filename=' . basename($dst_file));
readfile($dst_file);
@unlink($dst_file);
die;