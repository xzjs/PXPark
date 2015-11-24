<?php 

/**
 *  Created by PhpStorm.
 * User: syx
 * Date: 15/11/23
 * Time: 10:26am
 */
 namespace  Home\Controller;
 
use think\Controller;
use Think\Model;
use Org\Util\String;

class  DemandController extends  Controller
{
/**
 * 添加停车需求
 */
function add(){
	 
		$lon=I('param.lon');
		$lat=I('param.lat');
		$clon=I('param.current_lon');
		$clat=I('param.current_lat');
		$preference=I('param.preference');
		$pid=I('param.park_id');
		$uid=I('param.user_id');
		$cno=M()->query("SELECT px_car.no FROM px_user_car ,px_car WHERE px_car.id=px_user_car.car_id AND  px_user_car.user_id=$uid LIMIT 1
				");
	$car_no= $cno[0]['no'];
		$result=M()->execute("
				INSERT INTO  px_demand 
				(lon,lat,TIME,park_id,user_id,current_lon,current_lat,preference,car_no)VALUES 
				($lon,$lat,UNIX_TIMESTAMP(NOW()),$pid,$uid,$clon,$clat,$preference,'$car_no')
				");
		
		echo $result;
	}
	
	/**
	 * 是否停车成功
	 * 在用户进入车位后判断所在停车场和推荐的停车场一致，如果一致就传1，否则就传0
	 *
	 */
	function update(){
		$id=I('param.id');
		$flag=I('param.is_succes');
		$result=M()->execute("
    UPDATE px_demand SET is_success=$flag WHERE id=$id");
		echo $result;
	}
	
}
