<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class ParkrecordController extends Controller {
	public function index() {
		$this->show ( 'helloworld', 'utf-8' );
	}
	
	/**
	 * 获取近n天的停车和收费数据
	 * @param number $user_id 用户Id
	 * @param number $park_id 停车场Id
	 * @param number $time 查询天数
	 */
	public function count_car($user_id = 0, $park_id = 0, $time = 30) {
		if ($park_id != 0) {
			$condition=" and a.park_id=".$park_id;
		}else{
			$condition="";
		}
		$Model = new Model ();
		$time=strtotime("-30 day");
		$sql = 'SELECT a.money,c.type FROM px_parkrecord AS a,px_park AS b,px_car AS c WHERE b.user_id='.$user_id.' AND b.id=a.park_id AND c.id=a.car_id and a.end_time>'.$time.$condition.' ORDER BY a.id desc';
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
		return json_encode($detail);
	}
}