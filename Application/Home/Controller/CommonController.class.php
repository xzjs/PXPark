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
		$user_id=I('session.user')['user_id'];//$_SESSION('user')['id'];
		if($user_id){
			$result=$Pay->pay_info($user_id);
			$this->assign('data',$result);
			$this->display();
		}
	}
	
	public function add_pay() {
		$Pay = D ( 'Pay' );
		if ($Pay->create ()) {
			$user_id=$_SESSION('user')['id'];
			$Pay->user_id=$user_id;
			$pay=M('Pay');
			if(!$pay->where('user_id='.$user_id)->find()){
				$result = $Pay->add ();
				if ($result) {
					echo "<script>window.alert(\"添加成功！\"),location.href='".U('Common/pay_info?id='.$result)."';</script>";//添加成功
				} else {
					$this->error ( '数据添加错误！' );
				}
			}else{
				$result=$Pay->where('user_id=1')->save();
				if ($result) {
					echo "<script>window.alert(\"修改成功！\"),location.href='".U('Common/pay_info?id='.$result)."';</script>";//添加成功
				} else {
					$this->error ( '数据添加错误！' );
				}
			}
		} else {
			$this->error ( $Pay->getError () );
		}
		
	}

}