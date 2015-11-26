<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 天气获取控制器
 * createtime:2015年11月23日 下午4:39:03
 * @author xiuge
 */
class WeatherController extends Controller
{
    
    public function search() {
    	$city="北京";
    	$get_code_url="http://apistore.baidu.com/microservice/cityinfo";
    	$get_code_url.="?cityname=".$city;
    	$str =file_get_contents($get_code_url);
    	$arr =json_decode($str,TRUE);
    	$weather_code=$arr['retData']['cityCode'];
    	echo $weather_code;
    	$get_weather_url="http://api.map.baidu.com/telematics/v3/weather?location=青岛&output=json&ak=IxUGjzuEwf9e2zi4CudO91np";
    	$str =file_get_contents($get_weather_url,TRUE);
    	$arr =json_decode($str);
    	$result=$arr->results;
    	var_dump($result[0]->weather_data);//近四天天气
    	var_dump($result[0]->weather_data[0]->dayPictureUrl);
    }
}