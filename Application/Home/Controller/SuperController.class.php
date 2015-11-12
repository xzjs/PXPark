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
	 * 
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