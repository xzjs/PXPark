<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class AreaController extends Controller {
	public function index() {
	}
	
	/**
	 * 获取某用户的停车场位置的区划列表
	 * 
	 * @param unknown $user_id
	 *        	用户id
	 */
	public function get_area($user_id) {
	$user_type = M ()->query ( "select type from px_user where id=" . $user_id );
		$Area = M ( 'Area' );
		if ($user_type [0] ['type'] != 2) {
			$park_list = M ()->query ( "select id,name from px_park where user_id=" . $user_id . " order by area_id" );
			$json = array ();
			$i_park=-1;
			$i_jie = - 1;
			$i_xian = - 1;
			$i_shi = - 1;
			$i_sheng=-1;
			for($i = 0; $i < count ( $park_list ); $i ++) {
				$result_park=$park_list[$i];
				$result = M ()->query ( "select a.area_id as id,(select b.name from px_area as b where b.id=a.area_id)  as name from px_park as a where a.id=" . $result_park ['id'] ); // $Area->field('parent_id as id,name')->find($result_qu['id']);
				$result_jie = $result [0];
				$result = M ()->query ( "select a.parent_id as id,(select b.name from px_area as b where b.id=a.parent_id)  as name from px_area as a where a.id=" . $result_jie ['id'] ); // $Area->field('parent_id as id,name')->find($result_qu['id']);
				$result_xian = $result [0];
				$result = M ()->query ( "select a.parent_id as id,(select b.name from px_area as b where b.id=a.parent_id)  as name from px_area as a where a.id=" . $result_xian ['id'] );
				$result_shi = $result [0];
				$result = M ()->query ( "select a.parent_id as id,(select b.name from px_area as b where b.id=a.parent_id)  as name from px_area as a where a.id=" . $result_shi ['id'] );
				$result_sheng = $result [0];
				if ($result_sheng ['name'] != $tmp_sheng) {
					$i_sheng++;
					$i_shi =0;
					$i_xian = 0;
					$i_jie = 0;
					$i_park=0;
					$json [$i_sheng] ['name'] = $result_sheng ['name'];
					$json [$i_sheng] ['children'][$i_shi] ['name'] = $result_shi ['name'];
					$json [$i_sheng] ['children'][$i_shi] ['children'] [$i_xian] ['name'] = $result_xian ['name'];
					$json [$i_sheng] ['children'][$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['name'] = $result_jie ['name'];
					$json [$i_sheng] ['children'][$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['children'][$i_park]['name'] = $result_park ['name'];
				}elseif ($result_shi ['name'] != $tmp_shi) {
					$i_shi ++;
					$i_xian = 0;
					$i_jie = 0;
					$i_park=0;
					$json [$i_sheng] ['children'] [$i_shi] ['name'] = $result_shi ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['name'] = $result_xian ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['name'] = $result_jie ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['children'][$i_park]['name'] = $result_park ['name'];
				} elseif ($result_xian ['name'] != $tmp_xian) {
					$i_xian ++;
					$i_jie = 0;
					$i_park=0;
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['name'] = $result_xian ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['name'] = $result_jie ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['children'][$i_park]['name'] = $result_park ['name'];
				} elseif ($result_jie ['name'] != $tmp_jie) {
					$i_jie ++;
					$i_park=0;
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['name'] = $result_jie ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['children'][$i_park]['name'] = $result_park ['name'];
				} elseif($result_park['name']!=$tmp_park){
					$i_park++;
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_xian] ['children'] [$i_jie] ['children'][$i_park]['name'] = $result_park ['name'];
				}
				$tmp_sheng= $result_sheng['name'];
				$tmp_shi = $result_shi ['name'];
				$tmp_xian = $result_xian ['name'];
				$tmp_jie = $result_jie ['name'];
				$tmp_park=$result_park['name'];
			}
			echo json_encode ( $json );
			//var_dump(json_encode($json));
		} else {
			echo 2;
		}
	}
	
	/**
	 * 获取该id的所有子项列表
	 * @param unknown $id
	 */
	public function get_area_list($id) {
		if ($id) {
			$Area = M ( 'Area' );
			$Park=M('Park');
			$cdt_children ['parent_id'] = $id;
			$children = $Area->where ( $cdt_children )->field ( 'id,name' )->order ( 'id' )->select ();
			if ($children) {
				
				for($i = 0; $i < count ( $children ); $i ++) {
					$condition_children['parent_id']=$children [$i]['id'];
					$condition_park['area_id']=$children [$i]['id'];
					$has_children=$Area->where ($condition_children)->find ();
					$has_park=$Park->where ($condition_park)->find ();
					
					
					if ($has_children||$has_park){
						$json [$i] ['isParent'] = true;
					}else{
						$json [$i] ['isParent'] = false;
					}
					$json [$i] ['name'] = $children [$i] ['name'];
					$json [$i] ['id'] = $children [$i] ['id'];
					$json [$i] ['pId'] = $id;
				}
			}else{
				$condition_park['area_id']=$id;
				$park=$Park->where ($condition_park)->field ( 'id,name' )->order ( 'id' )->select ();
				
				for($i = 0; $i < count ( $park ); $i ++) {
					
					$json [$i] ['isParent'] = false;
					$json [$i] ['name'] = $park [$i] ['name'];
					$json [$i] ['id'] = $id."-".$park [$i] ['id'];
					$json [$i] ['pId'] = $id;
				}
			}
		} else {
			
			$Area = M ( 'Area' );
			$cdt_children ['level_type'] = 1;
			$children = $Area->where ( $cdt_children )->field ( 'id,name' )->order ( 'id' )->select ();
			for($i = 0; $i < count ( $children ); $i ++) {
				$json [$i] ['name'] = $children [$i] ['name'];
				$json [$i] ['id'] = $children [$i] ['id'];
				$json [$i] ['isParent'] = true;
				$json [$i] ['pId'] = 0;
			}
		}
		echo json_encode ( $json );
	}
}