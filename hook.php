<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/8/25
 * Time: 上午11:18
 */
//exec("cd d:/xampp/htdocs/wifi/ git pull ",$output);
error_reporting ( E_ALL );
$dir = '/var/www/html/PXPark/';//该目录为git检出目录
$handle = popen('cd '.$dir.' && git pull 2>&1','r');
$read = stream_get_contents($handle);
printf($read);


$myfile=fopen("log.txt","w");

fwrite($myfile,$read);
fclose($myfile);



pclose($handle);



