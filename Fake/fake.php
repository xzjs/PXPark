<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/12/3
 * Time: 下午4:16
 */
while(true){
    $url='http://192.168.4.96:48093/PXPark/index.php/Home/Fake/find';
    $html=file_get_contents($url);
    echo $html;
    sleep(1000);
}