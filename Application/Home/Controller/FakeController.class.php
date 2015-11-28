<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 模拟测试控制器
 * createtime:2015年11月27日 下午3:38:20
 * @author xiuge
 */
class FakeController extends Controller {
	
    public function index(){
    
    }
    
    
    public function find() {
    	
    }
    
    /**
     * 模拟车辆驶入某停车场
     */
    public function car_in( ) {
    	$rand=rand(0,99);
    	if($rand>97){
    		$is_success=1;
    	}else{
    		$is_success=0;
    	}
    	
    }
    
    public function car_out() {
    	$Parkrecord=A('Parkrecord');
    	$Parkrecord->leave();
    	
    }
    
}