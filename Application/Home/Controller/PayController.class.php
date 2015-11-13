<?php

namespace Home\Controller;

use Think\Controller;

class PayController extends Controller {
	public function index() {
		$this->show ( 'helloworld', 'utf-8' );
	}
	/**
	 * 获取该用户的支付信息
	 * 
	 * @param unknown $id        	
	 * @return unknown
	 */
	public function pay_info($id) {
		$Pay = M ( 'Pay' );
		$result = $Pay->find ( $id );
		return $result;
	}
}