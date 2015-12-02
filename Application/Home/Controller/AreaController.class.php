<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class AreaController extends Controller {
    public function index(){
       
    }
    
    public function get_area_list() {
    	$Area=M('Area');
    	$condition_sheng['level_type']=1;
    	$sheng=$Area->where($condition_sheng)->field('id,name')->order('id')->select();
    	$tmp=$sheng[1];
    	$sheng[1]=$sheng[14];
    	$sheng[14]=$tmp;
    	for($i=0;$i<count($sheng);$i++){
    		$json[$i]['name']=$sheng[$i]['name'];//14
    		$json[$i]['children'][0]['name']="";
    	}
    	$shi=M()->query("select name from px_area where level_type=2 and parent_id=370000");
    	for($i=0;$i<count($shi);$i++){
    		$json[1]['children'][$i]['name']=$shi[$i]['name'];//1
    		$json[1]['children'][$i]['children'][0]['name']="";
    	}
    	$qu=M()->query("select name from px_area where level_type=3 and parent_id=370200");
    	for($i=0;$i<11;$i++){
    		$json[1]['children'][1]['children'][$i]['name']=$qu[$i]['name'];
    	}
    	echo json_encode($json);
  
    }
    
    public function get_area() {
    	
    	
    	$json[0]['name']="山东";
    	$json[0]['children'][0]['name']="青岛";
    	$json[0]['children'][0]['children'][0]['name']="城阳区";
    	echo json_encode($json);
    	
    	
    	
    	
    	
    }

}