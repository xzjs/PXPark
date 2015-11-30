<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

/**
 * 天气获取控制器
 * createtime:2015年11月23日 下午4:39:03
 * @author xiuge
 */
class WeatherController extends Controller
{
    
    public function search($city_code=0) {
    	$Model = new Model ();
    	$sql = "select name from px_city where code=".$city_code;
    	$result = $Model->query ( $sql );
    	$city=$result[0]['name'];
    	$get_weather_url="http://api.map.baidu.com/telematics/v3/weather?location=".$city."&output=json&ak=IxUGjzuEwf9e2zi4CudO91np";
    	$str =file_get_contents($get_weather_url,TRUE);
    	$arr =json_decode($str);
    	$result=$arr->results;
    	if(date("H")>6&&date("H")<18){
    		$img_url=$result[0]->weather_data[0]->dayPictureUrl;
    	}else{
    		$img_url=$result[0]->weather_data[0]->nightPictureUrl;
    	}
    	$temperature=str_replace(' ', '', $result[0]->weather_data[0]->temperature);
    	$wind=$result[0]->weather_data[0]->wind;
    	
    	$weath=$result[0]->weather_data[0]->weather;
    	
    	$html_str="<img src=".$img_url." width='24' height='24'>
						<div>".$weath."</div>
						<div>".$wind."</div>
						<div>".$temperature."</div>";
    	echo $html_str;
    	
    	
    }
}