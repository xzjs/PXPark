<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/4
 * Time: 上午11:32
 */
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
echo "Connection to server sucessfully";
//查看服务是否运行
echo "Server is running: " + $redis->ping();
$num = $redis->lLen('point');
if ($num) {
    echo '已有数据';
} else {
    $myfile = fopen("little.txt", "r") or die("Unable to open file!");
// 输出单行直到 end-of-file
    while (!feof($myfile)) {
        $s=fgets($myfile);
        echo $s . "<br>";
        $redis->lPush('point',$s);
    }
    fclose($myfile);
    echo "读取完成";
}