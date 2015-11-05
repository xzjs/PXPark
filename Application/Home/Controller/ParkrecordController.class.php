<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class ParkrecordController extends Controller {
	public function index() {
		$this->show ( 'hello world', 'utf-8' );
	}
	
	/**
	 * 获取近n天的停车和收费数据
	 * @param number $user_id 用户Id
	 * @param number $park_id 停车场Id
	 * @param number $time 查询天数
	 */
	public function count_car( ) {
		if (I('param.park_id',0)!= 0) {
			$condition=" and a.park_id=".I('param.park_id');
		}
		if (I('param.user_id',0)!= 0) {
			$condition=" and b.user_id=".I('param.user_id');
		}
		$Model = new Model ();
		$time=strtotime("-".I('param.time',30)." day");
		$sql = 'SELECT a.money,c.type FROM px_parkrecord AS a,px_park AS b,px_car AS c WHERE b.id=a.park_id AND c.id=a.car_id and a.end_time>'.$time.$condition.' ORDER BY a.id desc';
		echo  $sql;
		$result = $Model->query ( $sql );
		$detail['small']=array("num"=>0,"money"=>0);
		$detail['small_free']=array("num"=>0,"money"=>0);
		$detail['big']=array("num"=>0,"money"=>0);
		$detail['big_free']=array("num"=>0,"money"=>0);
			
		foreach ($result as $value){
			if($value['type']==1&&$value['money']>0){
				$detail['small']['num']++;
				$detail['small']['money']+=$value['money'];
			}elseif($value['type']==1&&$value['money']==0){
				$detail['small_free']['num']++;
				$detail['small_free']['money']+=$value['money'];
			}elseif($value['type']==2&&$value['money']>0){
				$detail['big']['num']++;
				$detail['big']['money']+=$value['money'];
			}elseif($value['type']==2&&$value['money']==0){
				$detail['big_free']['num']++;
				$detail['big_free']['money']+=$value['money'];
			}
		}
		echo json_encode($detail); 
	}
	
	/**
	 * 获取某时间段内的车辆和收费情况
	 */
	public function count_income() {
		$condition="";
		if (I('param.park_id',0)!= 0) {
			$condition.=" and px_parkrecord.park_id=".I('param.park_id');
		}
		if(I('param.type',0)!=0){
			$condition.="and px_car.type=".I('param.type');
		}
		if(I('param.flag',0)!=0){
			$condition.=" and px_parkrecord.end_time is null";
		}
		if(I('param.user_id',0)!=0){
			$condition.=" and px_park.user_id=".I('param.user_id');
		}
		$in_time=strtotime(I('param.start_time'));
		$out_time=strtotime(I('param.end_time'));
		
		$Model = new Model ();
		$time=strtotime("-".$time." day");
		$sql= 'SELECT px_parkrecord.id,px_parkrecord.start_time,px_parkrecord.end_time,px_parkrecord.money,px_car.no,px_user.member_id, 
				px_parkrecord.end_time-px_parkrecord.start_time as time FROM px_parkrecord,px_car,px_park,px_user,px_user_car WHERE (px_parkrecord.start_time 
				between '.$in_time.' and '.$out_time.' OR px_parkrecord.end_time between '.$in_time.' and '.$out_time.
		')and px_park.id=px_parkrecord.park_id and px_parkrecord.car_id=px_car.id and px_car.id=px_user_car.car_id and px_user_car.user_id=px_user.id '.$condition;
		$result = $Model->query ( $sql );
		$json_array=array();
		for($i=0;$i<count($result);$i++){
			if(($result[$i]['start_time']>=$in_time)&&($result[$i]['start_time']>=$in_time))
				$json_array['in_num']++;
			if(($result[$i]['end_time']>=$in_time)&&($result[$i]['end_time']>=$in_time))
				$json_array['finish_num']++;
			$json_array['money']+=$result[$i]['money'];
			$json_array['cars'][$i]['car_no']=$result[$i]['no'];
			$json_array['cars'][$i]['start_time']=$result[$i]['start_time'];
			$json_array['cars'][$i]['end_time']=$result[$i]['end_time'];
			$json_array['cars'][$i]['time']=$result[$i]['time'];
			$json_array['cars'][$i]['money']=$result[$i]['money'];
			switch ($result[$i]['member_id']) {
				case 1:
					$json_array['cars'][$i]['member_id']='普通会员';
				break;
				case 2:
					$json_array['cars'][$i]['member_id']='白银会员';
					break;
				case 3:
					$json_array['cars'][$i]['member_id']='黄金会员';
					break;
				default:
					$json_array['cars'][$i]['member_id']='';
				break;
			}
		}
		echo json_encode($json_array);
	}
}