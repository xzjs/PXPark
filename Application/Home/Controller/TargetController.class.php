<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 停车场指数控制器
 * createtime:2015年11月23日 上午10:56:10
 * @author xiuge
 */
class TargetController extends Controller
{
    
    public function add($park_id,$num) {
    	$Target=D('Target');
    	$result1=$Target->where('park_id='.$park_id)->find();
    	if($result1){
    		$target=array("id"=>$result1['id'],"num"=>$num);
    	if($Target->create($target)){
    		$result = $Target->save();
    		if($result) {
    			$this->success('数据添加成功！');
    		}else{
    			$this->error('数据添加错误！');
    		}
    	}else{
    		$this->error($Target->getError());
    	}
    	}else{
    	$target=array("park_id"=>$park_id,"num"=>$num);
    	if($Target->create($target)){
    		$result = $Target->add();
    		if($result) {
    			$this->success('数据添加成功！');
    		}else{
    			$this->error('数据添加错误！');
    		}
    	}else{
    		$this->error($Target->getError());
    	}
    }
	
}
}