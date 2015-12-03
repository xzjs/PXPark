<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class AreaController extends Controller {
    public function index(){
       
    }
    
    public function get_area() {
    	
  
    }
	public function get_area_list($id) {
		if ($id) {
			$Area=M('Area');
			$condition_sheng ['parent_id'] = $id;
			$sheng = $Area->where ( $condition_sheng )->field ( 'id,name' )->order ( 'id' )->select ();
			$condition['parent_id']=$sheng[0]['id'];
			if($Area->where ( $condition )->find())
				$isParent=true;
			else
				$isParent=false;
			if($Area->where ( ))
			for($i = 0; $i < count ( $sheng ); $i++) {
				$json [$i] ['name'] = $sheng [$i] ['name'];
				$json [$i] ['id'] = $sheng [$i] ['id'];
				$json [$i] ['isParent'] = $isParent;
				$json [$i] ['pId'] = $id;
			}
		} else {

			$Area=M('Area');
			$condition_sheng ['level_type'] = 1;
			$sheng = $Area->where ( $condition_sheng )->field ( 'id,name' )->order ( 'id' )->select ();
			for($i = 0; $i < count ( $sheng ); $i++) {
				$json [$i] ['name'] = $sheng [$i] ['name'];
				$json [$i] ['id'] = $sheng [$i] ['id'];
				$json [$i] ['isParent'] = true;
				$json [$i] ['pId'] = 0;
			}
		}
		echo json_encode ( $json );
	}

}