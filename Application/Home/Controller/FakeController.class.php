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
    
    /**
     * 模拟寻找车位
     */
    public function find() {
    	
    }
    
    /**
     * 模拟车辆驶入某停车场
     */
    public function car_in( ) {
    	$condition['is_success']=array('exp','is null');
    	$car_list = M ('Demand')->where($condition)->select();
    	$rand=rand(0,count($car_list)-1);
    	$car=$car_list[$rand];//随机获取一个发布过停车请求的车
    	var_dump($car);
    	//var_dump($car);
    	$rand=rand(0,99);
    	//2%概率未能停入规划停车场
    	if($rand>97){
    		

    	//98%概率停入规划停车场
    	}else{
    		$condition_berth['park_id']=$car['park_id'];
    		$condition_berth['is_null']=0;
    		$berth_list=M('Berth')->where($condition_berth)->select();
    		$rand=rand(0,count($berth_list)-1);
    		$berth=$berth_list[$rand];//随机获取一个空车位
    		$berth['is_null']=1;
    		M('Berth')->save($berth);
    		
    		var_dump($berth);
			$Demand=A('Demand');
    		$result=$Demand->update($car['car_no'],$berth_no['no']);
    		var_dump($result);
    	}
    	
    }
    
    public function car_out() {
    	$Parkrecord=A('Parkrecord');
    	$Parkrecord->leave();
    	
    }
    
}