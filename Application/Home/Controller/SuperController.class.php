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
		$park=A('Park');
		
		$result=$park->statisticInfo(I('post.start_time'),I('post.end_time'),I('post.type'));
		//echo I('post.u_id');
		echo $result;
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