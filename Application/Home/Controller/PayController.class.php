<?php
namespace Home\Controller;
use Think\Controller;
class PayController extends Controller {
    public function index(){
        $this->show('helloworld','utf-8');
    }
    
    public function pay_info($id) {
    	$Pay=D('Pay');
    	if($id!=0){
    		$result=$Pay->find($id);
    	}
    	if($Pay->create()){
    		$result=$Pay->add();
    		
    	}
    }
    
    
}