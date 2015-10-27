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
	public function add() {
		$Recharge = D ( 'Rechargerecord' );
		if ($Recharge->create ()) {
			$result = $Recharge->add ();
			var_dump ( $result );
		} else {
			$this->error ( $Recharge->getError () );
		}
	}
	/**
	 * 查询停车记录
	 * 
	 * @param number $user_id
	 *        	用户id
	 */
	public function getList($user_id = 0) {
		$Recharge = M ( 'Rechargerecord' );
		$condition ['user_id'] = I ( 'param.user_id' );
		$result = $Recharge->where ( $condition )->select ();
		echo (count ( $result ) != 0) ? json_encode ( $result ) : null;
	}
}

