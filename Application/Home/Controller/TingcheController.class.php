<?php
/**
 * Created by PhpStorm.
 * User: syx
 * Date: 15/11/20
 * Time: 下午10:26
 */
namespace Home\Controller;

use Think\Controller;
use Think\Model;

/**
 * 普通管理员控制器
 * createtime:2015年11月19日 下午4:24:17
 * @author xiuge
 */
class tingcheController extends Controller{
	/**
	 * ajax获取停车场信息和收益状况
	 */
	public function get_park_info() {
		
		$Model = new Model ();
		$park_id=1;
		$user_id=1;
		if($park_id!=0){
			$condition=' where px_parkrecord.park_id='.$park_id;
		}else{
			if(param.user_id==0)
				$user_id=$_SESSION['user']['user_id'];
			else
				$user_id=user_id;
			$condition=',px_park where px_parkrecord.park_id=px_park.id and px_park.user_id='.$user_id ;
		}
		$condition_today=$condition." and px_parkrecord.end_time>".strtotime("today");
		$condition_tomonth=$condition." and px_parkrecord.end_time>".mktime(0,0,0,date('m'),1,date('Y'));
		$condition_toyear=$condition." and px_parkrecord.end_time>".mktime(0,0,0,1,1,date('Y'));
		$sql_income="select px_car.type,SUM(px_parkrecord.money) money from px_parkrecord,px_car".$condition_today." and px_car.id=px_parkrecord.car_id GROUP BY px_car.type order by px_car.type asc";
		$result_income[0] = $Model->query ( $sql_income );
		$sql_income="select px_car.type,SUM(px_parkrecord.money) money from px_parkrecord,px_car".$condition_tomonth." and px_car.id=px_parkrecord.car_id GROUP BY px_car.type order by px_car.type asc";
		$result_income[1] = $Model->query ( $sql_income );
		$sql_income="select px_car.type,SUM(px_parkrecord.money) money from px_parkrecord,px_car".$condition_toyear." and px_car.id=px_parkrecord.car_id GROUP BY px_car.type order by px_car.type asc";
		$result_income[2] = $Model->query ( $sql_income );
		$income_info[0]['date']=date("d号");
		$income_info[1]['date']=date("m月");
		$income_info[2]['date']=date("Y年");
		for($i=0;$i<3;$i++){
			$income_info[$i]['small']=$result_income[$i][0]['money'];
			$income_info[$i]['big']=$result_income[$i][1]['money'];
		}
		
		if($park_id!=0){
			$condition1=' where px_park.id='.$park_id;
		}else{
			if($user_id==0)
				$user_id=$_SESSION['user']['user_id'];
			else
				$user_id=$user_id;
			$condition1=' where px_park.user_id='.$user_id ;
		}
		$sql_park="select sum(px_park.total_num) total,sum(px_park.remain_num) remain from px_park ".$condition1;
		$result = $Model->query ( $sql_park );
		$sql_park="select count(*) cnt from px_parkrecord ".$condition." and px_parkrecord.money is null";
		$result_unpayed = $Model->query ( $sql_park );
		$sql_park="select count(*) cnt from px_parkrecord ".$condition." and px_parkrecord.money is not null";
		$result_payed = $Model->query ( $sql_park );
		if($park_id!=0){
			$condition2=' where px_berth.park_id='.$park_id;
		}else{
			if($user_id==0)
				$user_id=$_SESSION['user']['user_id'];
			else
				$user_id=$user_id;
			$condition2=' where px_berth.park_id=px_park.id and px_park.user_id='.$user_id ;
		}
		$sql="select count(*) cnt from px_berth ".$condition2." and px_berth.is_null=1";
		$result_used = $Model->query ( $sql);
		$income_info[3]['total']=$result[0]['total'];
		$income_info[3]['remain']=$result[0]['remain'];
		$income_info[3]['unpayed']=$result_unpayed[0]['cnt'];
		$income_info[3]['payed']=$result_payed[0]['cnt'];
		$income_info[3]['used']=$result_used[0]['cnt'];
		echo json_encode($income_info);
	}
}
		