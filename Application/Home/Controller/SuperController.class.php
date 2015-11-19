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
}