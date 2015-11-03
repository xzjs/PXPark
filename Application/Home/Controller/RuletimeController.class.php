<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 验证码控制器
 * @package Home\Controller
 */
class RuletimeController extends Controller
{/**
	 * 增加规则信息
	 */
	 public function add(){
	 	$ruletime=D('Ruletime');
	 	if(!$ruletime->create()){
	 		exit($ruletime->getError());
	 		
	 	}
	 	else{
	 		$result=$ruletime->add();
	 		if($result){
	 			$this->success ( '数据添加成功！' );//添加成功
	 			} else {
	 				$this->error ( '数据添加错误！' );//添加失败
	 			}
	 	}
	 
	 }
	
}