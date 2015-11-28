<?php

/**
 *  Created by PhpStorm.
 * User: syx
 * Date: 15/11/23
 * Time: 10:26am
 */
namespace Home\Controller;

use think\Controller;
use Think\Model;
use Org\Util\String;

class DemandController extends Controller {
	
	/**
	 * 获取所有停车需求
	 * time为正数是未来，为负数是过去，为0为当前的,比如-24就是过去24小时的数据，当前时间的就是is_success为空的数据,
	 * 因为没有大数据分析，所以未来24小时的数据就是过去24小时的数据
	 */
	public  function get_list(){
	 	//echo C('IP');
		$time=I('param.time',0);
		$type=I('param.type');
		if($time==0)
		{$condition="is_success IS NULL";}
		else 
			if($type==null||$type=="")
			{ //echo "ff";
				$condition="time>UNIX_TIMESTAMP(NOW())-$time*3600";
			}
		    else{
		    	if($type==0)
		    	{//echo "dd";
		    		$condition="time>UNIX_TIMESTAMP(NOW())-$time*3600 and is_success=0";
		    	}
		    	else
		    	{
		    		$condition="time>UNIX_TIMESTAMP(NOW())-$time*3600 and is_success=1";
		    	}
		    }
	//echo "select lon ,lat from px_demand where $condition";
	 $result=M()->query("select lon ,lat from px_demand where $condition");
	 $arr=array();
	 for($i=0;$i<count($result);$i++){
	 	$arr[$i]=array(
	 			"x"=>$result[$i]['lon'],
	 	"y"=>$result[$i]['lat'],
	 	);
	 }
	 $array=array(
	 		"data"=>$arr,
	 		);
	echo json_encode($array);
	 }
	
	/**
	 * 统计当前有多少人在寻找停车场
	 */
 	public function count() {
		$num = M ()->query ( "SELECT COUNT(id)as a FROM px_demand WHERE is_success IS NULL" );
		$n = $num [0] ['a'];
		// echo "f".$n;
		$percent = ( float ) $n / 100;
		
		echo $percent;
	}
	
	/**
	 * 添加停车需求
	 */
	public function add() {
		//$model = C('URL_MODEL');
		//echo $model;
		$lon = I ( 'param.lon' );
		$lat = I ( 'param.lat' );
		$clon = I ( 'param.current_lon' );
		$clat = I ( 'param.current_lat' );
		$preference = I ( 'param.preference' );
		$pid = I ( 'param.park_id' );
		$uid = I ( 'param.user_id' );
		$cno = M ()->query ( "SELECT px_car.no FROM px_user_car ,px_car WHERE px_car.id=px_user_car.car_id AND  px_user_car.user_id=$uid LIMIT 1
				" );
		$car_no = $cno [0] ['no'];
		$url = "http://api.map.baidu.com/geocoder/v2/?ak=IxUGjzuEwf9e2zi4CudO91np&callback=renderReverse&location=$lat,$lon&output=json&pois=1";
		$str = file_get_contents ( $url, TRUE );
		
			$string= substr(substr($str, 29), 0, -1) ;
		
	//	$test='{"status":0,"result":{"location":{"lng":116.32298703399,"lat":39.983424051248},"formatted_address":"北京市海淀区中关村大街27号1101-08室","business":"中关村,人民大学,苏州街","addressComponent":{"city":"北京市","country":"中国","direction":"附近","distance":"7","district":"海淀区","province":"北京市","street":"中关村大街","street_number":"27号1101-08室","country_code":0},"pois":[{"addr":"北京北京海淀海淀区中关村大街27号（地铁海淀黄庄站A1","cp":"NavInfo","direction":"内","distance":"0","name":"北京远景国际公寓(中关村店)","poiType":"房地产","point":{"x":116.3229458916,"y":39.983610361549},"tag":"房地产","tel":"","uid":"35a08504cb51b1138733049d","zip":""},{"addr":"海淀区中关村北大街27号","cp":"NavInfo","direction":"附近","distance":"25","name":"中关村大厦","poiType":"房地产","point":{"x":116.32285606105,"y":39.983568897877},"tag":"房地产;写字楼","tel":"","uid":"06d2dffdaef1b7ef88f15d04","zip":""},{"addr":"中关村大街29","cp":"NavInfo","direction":"北","distance":"62","name":"海淀医院激光整形美容部","poiType":"医疗","point":{"x":116.32317046798,"y":39.983016046485},"tag":"医疗;专科医院","tel":"","uid":"b1c556e81f27cb71b4265502","zip":""},{"addr":"中关村大街27号中关村大厦1层","cp":"NavInfo","direction":"附近","distance":"1","name":"中国人民财产保险中关村营业部","poiType":"金融","point":{"x":116.32298182382,"y":39.983416864194},"tag":"金融;投资理财","tel":"","uid":"060f5e53137d20d7081cc779","zip":""},{"addr":"北京市海淀区","cp":"NavInfo","direction":"东北","distance":"58","name":"北京市海淀医院-输血科","poiType":"医疗","point":{"x":116.322685383,"y":39.983092063819},"tag":"医疗;其他","tel":"","uid":"cf405905b6d82eb9b55f1e89","zip":""},{"addr":"北京市海淀区中关村大街27号中关村大厦二层","cp":"NavInfo","direction":"附近","distance":"0","name":"眉州东坡酒楼(中关村店)","poiType":"美食","point":{"x":116.32298182382,"y":39.983423774823},"tag":"美食","tel":"","uid":"2c0bd6c57dbdd3b342ab9a8c","zip":""},{"addr":"北京市海淀区中关村大街29号（海淀黄庄路口）","cp":"NavInfo","direction":"东北","distance":"223","name":"海淀医院","poiType":"医疗","point":{"x":116.32199368776,"y":39.982083099537},"tag":"医疗;综合医院","tel":"","uid":"fa01e9371a040053774ff1ca","zip":""},{"addr":"北京市海淀区中关村大街28号","cp":"NavInfo","direction":"西北","distance":"229","name":"海淀剧院","poiType":"休闲娱乐","point":{"x":116.32476945179,"y":39.982622137118},"tag":"休闲娱乐;电影院","tel":"","uid":"edd64ce1a6d799913ee231b3","zip":""},{"addr":"海淀黄庄地铁站旁","cp":"NavInfo","direction":"西北","distance":"375","name":"中发电子市场(中关村大街)","poiType":"购物","point":{"x":116.32529945204,"y":39.981537146849},"tag":"购物;家电数码","tel":"","uid":"69130523db34c811725e8047","zip":""},{"addr":"北京市海淀区知春路128号","cp":"NavInfo","direction":"西北","distance":"434","name":"泛亚大厦","poiType":"房地产","point":{"x":116.32600013033,"y":39.981516414381},"tag":"房地产;写字楼","tel":"","uid":"d24e48ebb9991cc9afee7ade","zip":""}],"poiRegions":[],"sematic_description":"北京远景国际公寓(中关村店)内0米","cityCode":131}}';
		$arr = json_decode ( $string,true);
		$business=explode(',',$arr['result']['business'])[0];
	//	echo $business;
	 	 $result = M ()->execute ( "
				INSERT INTO  px_demand 
				(lon,lat,TIME,park_id,user_id,current_lon,current_lat,preference,car_no,business)VALUES 
				($lon,$lat,UNIX_TIMESTAMP(NOW()),$pid,$uid,$clon,$clat,$preference,'$car_no','$business')
				" );
		
		echo $result;  
	}
	
	/**
	 * 是否停车成功
	 * 在用户进入车位后判断所在停车场和推荐的停车场一致，如果一致就传1，否则就传0
	 */
	public function update() {
		$id = I ( 'param.id' );
		$flag = I ( 'param.is_succes' );
		$result = M ()->execute ( "
    UPDATE px_demand SET is_success=$flag WHERE id=$id" );
		echo $result;
	}

	/**
	 * 获取返回用户的选择偏好数据
	 * @param unknown $user_id 用户Id
	 */
	public function preference($user_id) {
		$Demand=M('Demand');
		$result=$Demand->where('user_id='.$user_id)->field('preference,count(preference) as cnt')->group('preference')->order('count(preference) desc')->select();
		for($i=0;$i<5;$i++){
			if($result[$i]['cnt']){
			if($result[0]['cnt']!=0){
				$preference[$i]=$result[$i]['cnt']/$result[0]['cnt']*100;
			}
			}else{
				$preference[$i]=0;
			}
		} 
		echo json_encode($preference);
	}
	
	public function count_demand($lon,$lat) {
		$lon_floor=(floor($lon*100000))/100000;
		$lon_top=$lon_floor+0.00001;
		$lat_floor=(floor($lat*100000))/100000;
		$lat_top=$lat_floor+0.00001;
		
		
		$Demand=M('Demand');
		$codition['lon']=array('elt',$lon_top);
		$codition['lon']=array('egt',$lon_floor);
		$codition['lat']=array('elt',$lat_top);
		$codition['lat']=array('egt',$lat_floor);
		$result=$Demand->where($codition)->field('')->group('preference')->order('count(preference) desc')->select();
		
		
		
	}
	
}
