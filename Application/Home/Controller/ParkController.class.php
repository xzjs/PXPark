<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class ParkController extends Controller {
	public function index() {
		
	}
	/**
	 * 
	 */
	function coming_zhishu_line(){
		$now = strtotime ( "now " );
		$cout_start;
		$cut_time = $now - 24 * 60 * 60;
		$now_h = date ( "H", $now )+1 ;
		        $p=0;
				$n=$now_h-1;
				for($h = 23; $h >=0; $h --) {
					
					if($n-$p>=0)
					$ti[$h] = $n-$p;
					else 
					$ti[$h] = $n-$p+24;
					$p++;
					
				}
				for ($j=0;$j< 24; $j ++){
					$time[$j]=$ti[$j].":00";
					//echo "t:".$time[$j]."<br>";
				}
				for($h = 0; $h < 24; $h ++) {
					$sum [$h] = 0;
				}
				for($h = 0; $h < 24; $h ++) {
					$sum_num[$h] = 0;
				}
				for($h = 0; $h < 24; $h ++) {
					$value[$h] = 0;
				}
				$result=M()->query("SELECT num ,time FROM px_target WHERE
						$cut_time<=time ORDER BY TIME ");
				//echo "ctime".$cut_time;
				for($j=0;$j<count ( $result );$j++){
					$shour = date ( "H", $result [$j] ['time'] );
					if ($shour > $now_h)
						$ns = $shour - $now_h;
					else
					{
						$ns = $shour - $now_h + 24;
					}
				//	echo "ns:".$ns."<br>";
					$sum_num[$ns]++;
					//	echo "sum_mum:".$sum_num[$ns]."<br>";
						
					$sum[$ns]=$sum[$ns]+$result [$j] ['num'];
					//	echo "sum:".$sum[$ns]."<br>";
				}
				//echo "num:".count($result)."<br>";
				for($h = 0; $h < 24; $h ++) {
					$value[$h] = round($sum[$h]/$sum_num[$h],2)*10;
					//echo "valeu:".$value[$h]."<br>";
				}
				$array=array(
						"time"=>$time,
						"value"=>$value,
				);
				//echo "ww".json_encode($array);
				return  json_encode($array);
	}
	/**
	 * 获取日期段内停车指数
	 * 
	 */
	function zhishu_line($stime,$etime){
	
		$s=$stime;
		$e=$etime;
		$result=M()->query("SELECT num ,time FROM px_target WHERE
				$stime<=time AND time<=$etime+86400 ORDER BY TIME ");
		
	    $i=0;
		$num=0;
		$time;
		$value;
		$sum;
		$sum_num;
		while($s<=$e){
				
			$time[$i]=date ( "m-d",$s);
			$s=$s+86400;
			
			$i++;
			$num++;
		}
	//	echo "nuddm:".$num."<br>";
		for($h = 0; $h < $num; $h ++) {
			$sum [$h] = 0;
		}
		for($h = 0; $h < $num; $h ++) {
			$sum_num[$h] = 0;
		}
		for($h = 0; $h < $num; $h ++) {
			$value[$h] = 0;
		}
		for($j=0;$j<count ( $result );$j++){
			$d=date('Y-m-d', $result [$j] ['time']);
			//echo "d:".$d."<br>";
			$ns=(int)((strtotime($d)-$stime)/86400);
			//echo "ns:".$ns."<br>";
			$sum_num[$ns]++;
		//	echo "sum_mum:".$sum_num[$ns]."<br>";
			
			$sum[$ns]=$sum[$ns]+$result [$j] ['num'];
		//	echo "sum:".$sum[$ns]."<br>";
		}
		//echo "num:".count($result)."<br>";
		for($h = 0; $h < $num; $h ++) {
			$value[$h] = round($sum[$h]/$sum_num[$h],2)*10;
		//	echo "valeu:".$value[$h]."<br>";
		}
		$array=array(
				"time"=>$time,
				"value"=>$value,
		);
		//echo "ww".json_encode($array);
		return  json_encode($array);
	}
	/**
	 * 获取各类停车场数量
	 */
	function get_parks_num(){
		$result=M()->query("SELECT id ,TYPE FROM px_park ");
		$pu_n=0;
		$lu_n=0;
		$ge_n=0;
		$ch_n=0;
		for($i=0;$i<count($result);$i++){
		if ($result[$i]['type']==1){$pu_n++;}
		if($result[$i]['type']==2){$lu_n++;}
		if ($result[$i]['type']==3){$ge_n++;}
		}
		$array=array(
				"pu"=>$pu_n,
				"lu"=>$lu_n,
				"ge"=>$ge_n,
				"ch"=>$ch_n,
		);
		return  $array;
	}
	
	/**
	 * 获取停车场列表或者指定停车场
	 * @param number $user_id 用户Id
	 * @param number $park_id 停车场 Id
	 * @param number $type 停车场类型
	 */
	public function status() {
		$park=M('Park');
		if(I('param.type',0)!=0){
			$condition['type']=I('param.type');
		}
		if(I('param.park_id',0)!=0){
			$condition['id']=I('param.park_id');
		}
		$result = $park->where($condition)->field('name,total_num,remain_num,img')->select();
		for($i=0;$i<count($result);$i++){
			$result[$i]['img']=C('IP').__ROOT__.C('PARK_IMG_PATH').$result[$i]['img'];//图片链接url
		}
		$data['data']=$result;
		echo json_encode($data);
	}
	
	/**
	 * 添加停车场信息
	 */
	public function add(){
		$park = D ( 'Park' );
		if ($park->create ()) {
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->rootPath = '.'.C('PARK_IMG_PATH'); // 设置附件上传根目录
			$upload->autoSub = false;
			// 上传文件
			$info = $upload->upload();
			$park->img=$info[0]['savename'];
			$park->licence_img=$info[1]['savename'];
			$park->id_img=$info[2]['savename'];
			$park->remain_num = 0;
			$park->user_id=session('name');
			$result = $park->add();
			if ($result) {
				$this->success ( '数据添加成功！' );//添加成功
			} else {
				$this->error ( '数据添加错误！' );//添加失败
			}
		} else {
			$this->error ( $park->getError () );//验证失败
		}
	}
	
	/**
	 * 根据经纬度获取周围一定距离的停车场列表
	 * @param number $lon 经度
	 * @param number $lat 纬度
	 * @return Ambigous <mixed, boolean, string, NULL, multitype:, unknown, object>目地停车场列表
	 */
	public function getList($lon = 0, $lat = 0) {
		$distance_lon = 180;
		$distance_lat = 180;
		$condition ['lon'] = array (
				array (
						'gt',
						$lon - $distance_lon 
				),
				array (
						'lt',
						$lon + $distance_lon 
				) 
		);
		$condition ['lat'] = array (
				array (
						'gt',
						$lat - $distance_lat 
				),
				array (
						'lt',
						$lat + $distance_lat 
				) 
		);
		$park = M ( 'Park' );
		$result = $park->where ( $condition )->field ( 'id,name,lon,lat,price,remain_num as remain,total_num as total' )->select ();
		$distance = array ();
		for($i = 0; $i < count ( $result ); $i ++) {
			$distance [] = $this->getDistance ( $lat, $lon, $result [$i] ['lat'], $result [$i] ['lon'] );
		}
		array_multisort ( $distance, SORT_ASC, $result ); // 按距离排序
		
		return $result;
	}
	
	/**
	 * 根据停车场id查询该停车场详细信息
	 * 
	 * @param number $park_id
	 *        	停车场id
	 */
	public function getDetail($park_id = 0) {
		$park = M ( 'Park' );
		$condition ['id'] = $park_id;
		$result = $park->where ( $condition )->field ( 'id,name,lon,lat,price,remain_num as remain,total_num as total,
				type,address,img' )->select ();
		return $result;
	}
	
	/**
	 * 获取停车记录
	 * 
	 * @param number $use_id
	 *        	用户id
	 */
	public function getRecord($user_id = 0) {
		$Model = new Model ();
		$sql = 'select c.name as park_name,d.no as car_no,a.start_time,a.end_time,a.money from px_parkrecord as a, 
				px_user_car as b,px_park as c,px_car as d where a.car_id=b.car_id and b.user_id=' . $user_id . ' 
						and b.status=1 and a.park_id=c.id and b.car_id=d.id order by a.id';
		$result = $Model->query ( $sql );
		return  $result;
		//echo (count ( $result ) != 0) ? json_encode ( $result ) : null;
	}
	
	/**
	 * 根据两点间的经纬度计算距离
	 *
	 * @param float $lat
	 *        	纬度值
	 * @param float $lng
	 *        	经度值
	 */
	function getDistance($lat1, $lng1, $lat2, $lng2) {
		$earthRadius = 6367000; // approximate radius of earth in meters
		
		$lat1 = ($lat1 * pi ()) / 180;
		$lng1 = ($lng1 * pi ()) / 180;
		
		$lat2 = ($lat2 * pi ()) / 180;
		$lng2 = ($lng2 * pi ()) / 180;
		
		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow ( sin ( $calcLatitude / 2 ), 2 ) + cos ( $lat1 ) * cos ( $lat2 ) * pow ( sin ( $calcLongitude / 2 ), 2 );
		$stepTwo = 2 * asin ( min ( 1, sqrt ( $stepOne ) ) );
		$calculatedDistance = $earthRadius * $stepTwo;
		
		return round ( $calculatedDistance );
	}
	
}