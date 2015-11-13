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
class CommonController extends Controller{
	
	public function user_info() {
		$user = A( 'User' );
		$result=$user->detail(I('param.id',0));
		if($result==-1)
			$this->display('user_register');
		else{
			$this->assign('data',$result);
			$this->display('user_update');
		}
	}
	
	public function web_register() {
		$user=A('User');
		$x=I('param.cardfile');
		$result=$user->web_register(I('param.username'),I('param.password'),I('param.factname'),I('param.cardNo'),I('param.phone'),I('param.message'));
	}
	
	public function pay_info() {
		$Pay=A('Pay');
		$result=$Pay->pay_info();
	}

}