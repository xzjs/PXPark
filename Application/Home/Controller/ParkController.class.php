<?php

namespace Home\Controller;

use Think\Controller;

class ParkController extends Controller {
	public function index() {
	}
	
	/**
	 * 根据经纬度获取周围一定距离的停车场列表
	 * 
	 * @param number $lon
	 *        	经度
	 * @param number $lat
	 *        	纬度
	 */
	public function getList($lon = 0, $lat = 0) {
		if (($lon != 0) && ($lat != 0)) {
			$distance_lon = 0.1;
			$distance_lat = 0.1;
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
			$result = $park->where ( $condition )->select ();
			$distance=array();
			for ($i=0;$i<count($result);$i++){
				$distance[]=getDistance($lat,$lon,$result[$i]['lat'],$result[$i]['lon']);
			}
			array_multisort($distance, SORT_DESC, $result);//按距离排序
			$result = (count ( $result ) != 0) ? json_encode ( $result ) : null;
		} else {
			$result = null;
		}
		echo $result;
	}
	
	/**
	 * 根据停车场id查询该停车场详细信息
	 * @param number $park_id
	 *        	停车场id
	 */
	public function getDetail($park_id = 0) {
		$park = M ( 'Park' );
		$condition ['id'] = $park_id;
		$result = $park->where ( $condition )->select ();
		echo (count ( $result ) != 0) ? json_encode ( $result ) : null;
	}
	
	/**
	 * 获取停车记录
	 * @param number $use_id 用户id
	 */
	public function getRecord($user_id = 0) {
		$Model = new Model ();
		$sql = 'select * from px_parkrecord as a, px_user_car as b where a.car_id=b.px_car_id and b.px_user_id='
				.$user_id.'and b.status=1 order by a.id';
		$result = $Model->query ( $sql );
		echo (count ( $result ) != 0) ? json_encode ( $result ) : null;
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