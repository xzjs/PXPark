<?php
/**
 * 规则控制器
 */
namespace Home\Controller;
use Think\Controller;
class  RuleController extends Controller{
	/**
	 * 增加规则信息
	 */
public function  add(){
	$s='2015-10-26 10:00:00';
	//echo (strtotime($s));
	$rule_info = D('Rule');
	if (!$rule_info->create()){
		// 如果创建失败 表示验证没有通过 输出错误提示信息
		exit($rule_info->getError());
	}else{
		$rule_info->name=I('post.name');
		$rule_info->park_id=(int)I('post.park_id');
		$rule_info->end_date=strtotime(I('post.ruletype_id'));
		$rule_info->start_date=strtotime(I('post.ruletype_id'));
		
		$result=$rule_info->add();
		if ($result) {
			$this->success ( '数据添加成功！' );//添加成功
		} else {
			$this->error ( '数据添加错误！' );//添加失败
		}
	}
	
	
}
}