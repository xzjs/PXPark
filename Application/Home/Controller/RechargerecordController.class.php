<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 充值记录管理类
 * createtime:2015年10月26日 下午4:07:17
 *
 * @author xiuge
 */
class RechargerecordController extends Controller {
	public function index() {
	}
	
	/**
	 * 添加充值记录
	 */
	public function add($id,$money,$type) {
		$Recharge = D ( 'Rechargerecord' );
		$recharge=array("user_id"=>$id,"money"=>$money,"type"=>$type);
		if ($Recharge->create ($recharge)) {
			$result = $Recharge->add ();
			//var_dump ( $result );
		} else {
			$this->error ( $Recharge->getError () );
		}
	}
	/**
	 * 查询充值记录
	 * 
	 * @param number $user_id
	 *        	用户id
	 */
	public function getList($user_id = 0,$page=0,$num=0) {
		$Recharge = M ( 'Rechargerecord' );
		$condition ['user_id'] = $user_id;
		if($page==0&&$num==0){
			$result = $Recharge->where ( $condition )->field('type,money,time')->select ();
		}else{
			$result = $Recharge->where ( $condition )->page($page,$num)->field('type,money,time')->select ();
		}
		for($i=0;$i<count($result);$i++){
			$result[$i]['time']=date("Y-m-d h:i:sa",$result[$i]['time']);
			if($result[$i]['type']==1)
				$result[$i]['type']="支付宝";
			elseif($result[$i]['type']==2)
			$result[$i]['type']="微信";
			elseif($result[$i]['type']==3)
			$result[$i]['type']="银行卡";
		}
		return $result;
	}
}

