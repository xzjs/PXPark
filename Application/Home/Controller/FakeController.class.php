<?php

namespace Home\Controller;

use Think\Controller;
use Think\Exception;

/**
 * 模拟测试控制器
 * createtime:2015年11月27日 下午3:38:20
 * 
 * @author xiuge
 */
class FakeController extends BaseController {
	public function index() {
	}
	
	/**
	 * 模拟寻找车位
	 */
	public function find() {
		$position = $this->get_position ();
		$lon = $position ['lng'];
		$lat = $position ['lat'];
		
		$ParkController = A ( 'Park' );
		$park_list = $ParkController->getList ( $lon, $lat );
		//$target_park =$park_list[rand(0,count($park_list)-1)];
		$park_id=$park_list[0]['id'];//$target_park['id'];
		
		
		$user_id = $this->get_user_id ();
		
		$preference = rand ( 1, 5 );
		
		$position = $this->get_position ();
		$current_lon = $position ['lng'];
		$current_lat = $position ['lat'];
		
		$DemandController = A ( 'Demand' );
		$result = $DemandController->add ( $lon, $lat, $park_id, $user_id, $preference, $current_lon, $current_lat );
		echo ($result?$park_id:'失败').','.date('Y-m-d H:i:s');
		//$this->success("添加数据成功！");
	}
	
	/**
	 * 模拟车辆驶入某停车场
	 */
	public function car_in() {
            $condition ['is_success'] = array (
                'exp',
                'is null'
            );
            $sql="SELECT a.id,a.park_id,a.car_no FROM px_demand AS a WHERE TIME=(SELECT MAX(b.time) FROM px_demand AS b WHERE a.car_no=b.car_no AND b.is_success IS NULL) limit 0,15 ";
            $demand_list = M ()->query($sql);
            $rand = rand ( 0, count ( $demand_list ) - 1 );
            $demand = $demand_list [$rand]; // 随机获取一个发布过停车请求的车

            if ($demand) {
                $rand = rand ( 0, 99 );
                // 2%概率未能停入规划停车场
                if ($rand > 97) {
                    $condition_berth ['park_id'] = array (
                        'neq',
                        $demand['park_id']
                    );
                    $condition_berth ['is_null'] = 0;
                    // 98%概率停入规划停车场
                } else {
                    $condition_berth ['park_id'] = $demand['park_id'];
                    $condition_berth ['is_null'] = 0;
                }
                $berth_list = M ( 'Berth' )->where ( $condition_berth )->select ();
                if($berth_list){

                    $rand = rand ( 0, count ( $berth_list ) - 1 );
                    $berth = $berth_list [$rand]; // 随机获取一个空车位

                    $condition_car['no']=$demand['car_no'];
                    $car_info=M('Car')->field('id,type')->where($condition_car)->find();


                    $Parkrecord=A('Parkrecord');
                    $Parkrecord->add($berth['park_id'],$car_info['id'],$car_info['type'],$berth['id']);//增加停车记录

                    $Demand = A ( 'Demand' );
                    $result = $Demand->update ( $demand['car_no'], $berth['no'] );


                }else{

                    echo "id为".$demand['park_id']."的停车场已无车位，停车失败！";
                }
            }else{
                echo "Demand列表为空";
            }
            //$this->success ( '数据添加成功！' );

	}
					
					
					
	public function car_out() {
		$condition['start_time']=array('exp','is not null');
		$condition['berth_id']=array('exp','is not null');
		$condition['end_time']=array('exp','is null');
		$record_list=M('Parkrecord')->where($condition)->select();
		$rand=rand(0,count($record_list)-1);
		$record=$record_list[$rand];
		
		$car=M('Car')->find($record['car_id']);
		$berth=M('Berth')->find($record['berth_id']);
		
		$money=rand(0,20);
		
		$Parkrecord = A ( 'Parkrecord' );
		$Parkrecord->leave ($car['no'],$berth['no'],$money);
        //$this->success('数据添加成功！');
	}
	
	/**
	 * 模拟用户充值
	 */
	public function recharge() {
		$user_id = $this->get_user_id ();
		$money = rand ( 1, 100 );
		$type = rand ( 1, 3 );
		$RechargerecordController = A ( 'Rechargerecord' );
		$result = $RechargerecordController->add ( $user_id, $money, $type );
		echo $result ? '1,充值成功,' : '0,充值失败,';
		echo date ( 'Y-m-d H:i:s' );
	}
	
	/**
	 * 随机获取一个用户id
	 * 
	 * @return mixed 用户id
	 */
	private function get_user_id() {
		$UserController = A ( 'User' );
		$user_list = $UserController->get_list ( 1 );
		$rand = rand ( 0, count ( $user_list ) - 1 );
		$user = $user_list [$rand];
		//$user = $UserController->get_list ( 1 );
		if ($user ['Car'] == null) {
			$flag = true;
			while ( $flag ) {
				$car_no = '鲁B' . rand ( 10000, 99999 );
				$type = rand ( 1, 2 );
				$CarController = A ( 'Car' );
				$result = $CarController->add_car_in_usrcar ( $user ['id'], $type, $car_no );
				$flag = $result == 0 ? false : true;
			}
		}
		$user_id = $user_list [$rand]['id'];
		return $user_id;
	}
}