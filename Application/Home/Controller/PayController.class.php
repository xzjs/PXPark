<?php
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
    public function index(){
        $this->show('helloworld','utf-8');
    }
    /**
     * 获取该用户的支付信息
     * @param unknown $id
     * @return unknown
     */
	public function pay_info($id) {
		$result = $Pay->find ( $id );
		return $result;
	}
	
	/**
	 * 新增支付信息
	 */
	public function add_pay() {
		$Pay = D ( 'Pay' );
		if ($Pay->create ()) {
			$result = $Pay->add ();
		}
	}
    
    
}