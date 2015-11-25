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
	 	echo C('IP');
		$time=I('param.time',0);
		if($time==0)
			$condition="is_success IS NULL";
		else 
			$condition="time>UNIX_TIMESTAMP(NOW())-86400";
	
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
		$model = C('URL_MODEL');
		echo $model;
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
		$result = M ()->execute ( "
				INSERT INTO  px_demand 
				(lon,lat,TIME,park_id,user_id,current_lon,current_lat,preference,car_no)VALUES 
				($lon,$lat,UNIX_TIMESTAMP(NOW()),$pid,$uid,$clon,$clat,$preference,'$car_no')
				" );
		
		//echo $result;
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
