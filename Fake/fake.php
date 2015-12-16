<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/12/3
 * Time: 下午4:16
 */
while(true){
    $num=rand(0,3);
    $url;
    switch($num){
        case 0:
            $url='http://192.168.4.96:48093/PXPark/index.php/Home/Fake/find';
            break;
        case 1:
            $url='http://192.168.4.96:48093/PXPark/index.php/Home/Fake/car_in';
            break;
        case 2:
            $url='http://192.168.4.96:48093/PXPark/index.php/Home/Fake/car_out';
            break;
        case 3:
            $url='http://192.168.4.96:48093/PXPark/index.php/Home/Fake/recharge';
            break;
    }
    try{
        $html=file_get_contents($url);
    }catch (Exception $ex){
        echo $ex;
    }
    echo $html;
    sleep(60);
}