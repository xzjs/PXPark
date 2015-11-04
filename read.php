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
echo "Server is running: "+ $redis->ping();