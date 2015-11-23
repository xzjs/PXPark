<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/2
 * Time: 下午10:26
 */
namespace Home\Controller;

use Think\Controller;

/**
 * 超级管理员控制器
 * @package Home\Controller
 */
class SuperController extends Controller{
	/**
	 * 停车类型收益与入口流量统计
	 * 
	 */
	function statisticInfo(){
		$user_id=1;
		//$Model = new Model ();
		$last_month=strtotime("last Month")+mktime(0,0,0,date("m"),date("d")+1,date("Y"))-time();
		//var_dump(date("Y-m-d h:i:sa",$last_month));
		$sql_small = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE c.type=1 AND b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d')";
		$result_small = M()->query ( $sql_small );
		
		$sql_big = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE c.type=2 AND b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d') ORDER BY
			FROM_UNIXTIME(a.start_time,'%m-%d') DESC LIMIT 0,30";
		$result_big = M()->query ( $sql_big );
		
		for($i=0;$i<30;$i++){
			$date=date("m-d", strtotime("-".(29-$i)." Days"));
			$colum_chart[$i]['date']=$date;
			for($j=0;$j<count($result_small);$j++){
				if($result_small[$j]['atime']==$date){
					$colum_chart[$i]['small']=$result_small[$j]['cnt'];
					$colum_chart[$i]['money']=$result_small[$j]['money'];
				}
			}
			for($j=0;$j<count($result_big);$j++){
				if($result_big[$j]['atime']==$date){
					$colum_chart[$i]['big']=$result_big[$j]['cnt'];
					$colum_chart[$i]['money']+=$result_big[$j]['money'];
				}
			}
		}
		//var_dump($colum_chart);
		
		$sql_pie = "SELECT COUNT(*) cnt, c.type,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_month." GROUP BY c.type,a.money";
		$result_pie = M()->query ( $sql_pie );
		//var_dump($result_pie);
		$pie=array("small"=>0,"small_free"=>0,"big"=>0,"big_free"=>0);
		for($i=0;$i<count($result_pie);$i++){
			if(($result_pie[$i]['money']!=0)&&($result_pie[$i]['type']==1))
				$pie['small']+=$result_pie[$i]['cnt'];
			elseif (($result_pie[$i]['money']==0)&&($result_pie[$i]['type']==1))
			$pie['small_free']+=$result_pie[$i]['cnt'];
			elseif (($result_pie[$i]['money']!=0)&&($result_pie[$i]['type']==2))
			$pie['big']+=$result_pie[$i]['cnt'];
			elseif (($result_pie[$i]['money']==0)&&($result_pie[$i]['type']==2))
			$pie['big_free']+=$result_pie[$i]['cnt'];
		}
		//var_dump($pie);
		
		$last_day=strtotime("-1 Day")+mktime(date("H")+1,0,0,date("m"),date("d"),date("Y"))-time();
		//var_dump(date("Y-m-d h:i:sa",$last_day));
		$sql_line="SELECT FROM_UNIXTIME(a.start_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_day." GROUP  BY FROM_UNIXTIME(a.start_time,'%H')";
		$result_line = M()->query ( $sql_line );
		$sql_line="SELECT FROM_UNIXTIME(a.end_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.end_time>".$last_day." GROUP  BY FROM_UNIXTIME(a.end_time,'%H')";
		$result_line_end = M()->query ( $sql_line );
		//var_dump($result_line);
		for($i=0;$i<24;$i++){
			$time=date("H", strtotime("-".(23-$i)." Hours"));
			$line_chart[$i]['time']=$time;
			for($j=0;$j<count($result_line);$j++){
				if($result_line[$j]['atime']==$time){
					$line_chart[$i]['cnt']=$result_line[$j]['cnt'];
				}
			}
			for($j=0;$j<count($result_line_end);$j++){
				if($result_line_end[$j]['atime']==$time){
					$line_chart[$i]['cnt_end']=$result_line_end[$j]['cnt'];
				}
			}
		}
		//var_dump($line_chart);
		$this->assign ( 'columInfo', json_encode ( $colum_chart ) );
		$this->assign ( 'pieInfo', json_encode ( $pie ) );
		$this->assign ( 'lineInfo', json_encode ( $line_chart ) );
		$this->display ();
	}
	
	/**
	 * 获取单个用户停车历史记录给前端用
	 */
	function getparkrecord(){
		$user=A('User');
		
		$result=$user->getparkrecord(I('post.u_id'));
		//echo I('post.u_id');
		echo $result;
	}
	
	/**
	 * 获取所有普通用户信息
	 * 目前荣誉等级为空
	 */
	 function persons_info(){
		$user=A('User');
		$result=$user->persons_info();
		echo $result;
	}
	
	/**
	 * 停车厂综合管控
	 */
	function cManager(){
		$park =A('Park');
		$result=$park->compreManager(I('post.type'));
		echo $result;
	}
	
	/**
	 * 停车状态
	 */
	function chewei_status(){
	$park = A('Park');
		//echo "suer".$
		$result=$park->chewei_status(I('post.type'));
		//echo "rr".I('post.type');
		echo $result;
	}
	
	/**
	 * 未来24停车指数
	 */
	function  coming_zhishu_line(){
		$park = A('Park');
		$result=$park->coming_zhishu_line();
		echo $result;
	}
	/**
	 * 获取日期段内停车指数
	 */
	function zhishu_line(){
		 $park = A('Park');
		$stime=strtotime (I('post.stime'));
		$etime=strtotime(I('post.etime'));
		$result=$park->zhishu_line($stime,$etime);
		
		echo $result; 
	//	echo "hah";
	}
	/**
	 * 获取个停车场个数
	 */
 function pie_parks() {

 	$park = A('Park');
 	$result= $park->get_parks_num();
 	
 	//echo $array;
 	echo json_encode( $result);
	
}

	public function tradeManager($type = 0)
	{
		$Park = D('Park');
		$condition = $type ? array('type' => $type) : array();
		$car_id_array = $Park->where($condition)->getField('id', true);
		$ParkrecordController = A('Parkrecord');
		$parkrecord_array = $ParkrecordController->complete_list($car_id_array);
		for ($i = 0; $i < count($parkrecord_array); $i++) {
			$Car = D('Car');
			$parkrecord_array[$i]['car_user'] = $Car->relation(true)->find($parkrecord_array[$i]['car_id']);
			$parkrecord_array[$i]['park_user']=$Park->relation(true)->find($parkrecord_array[$i]['park_id']);
		}

		var_dump($parkrecord_array);
	}
}