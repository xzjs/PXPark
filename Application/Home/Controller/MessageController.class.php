<?php
namespace Home\Controller;

use Think\Controller;
class MessageController extends Controller{
	/**
	 * 添加意见反馈
	 */
	function  add(){
		$messge=D("Message");
		if(!$messge->create()){
			exit($messge->getError());
		}
		else {
			$result=$messge->add();
			if ($result) {
				$this->success ( '数据添加成功！' );//添加成功
			} else {
				$this->error ( '数据添加错误！' );//添加失败
			}
		}
	}
}