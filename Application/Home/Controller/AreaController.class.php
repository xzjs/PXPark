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
			$area_id_list = M ()->query ( "select px_park.area_id as id,px_area.name from px_park,px_area where px_park.user_id=" . $user_id . " and px_area.id=px_park.area_id order by px_park.area_id" );
			$json = array ();
			$i_qu = - 1;
			$i_shi = - 1;
			$i_sheng = - 1;
			
			
			
			
			for($i = 0; $i < count ( $area_id_list ); $i ++) {
				$result_qu = $area_id_list [$i];
				$result = M ()->query ( "select a.parent_id as id,(select b.name from px_area as b where b.id=a.parent_id)  as name from px_area as a where a.id=" . $result_qu ['id'] ); // $Area->field('parent_id as id,name')->find($result_qu['id']);
				$result_shi = $result [0];
				$result = M ()->query ( "select a.parent_id as id,(select b.name from px_area as b where b.id=a.parent_id)  as name from px_area as a where a.id=" . $result_shi ['id'] );
				$result_sheng = $result [0];
				if ($result_sheng ['name'] != $tmp_sheng) {
					$i_sheng ++;
					$i_shi = 0;
					$i_qu = 0;
					$json [$i_sheng] ['name'] = $result_sheng ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['name'] = $result_shi ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_qu] ['name'] = $result_qu ['name'];
				} elseif ($result_shi ['name'] != $tmp_shi) {
					$i_shi ++;
					$i_qu = 0;
					$json [$i_sheng] ['children'] [$i_shi] ['name'] = $result_shi ['name'];
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_qu] ['name'] = $result_qu ['name'];
				} elseif ($result_qu ['name'] != $tmp_qu) {
					$i_qu ++;
					$json [$i_sheng] ['children'] [$i_shi] ['children'] [$i_qu] ['name'] = $result_qu ['name'];
				}
				$tmp_sheng = $result_sheng ['name'];
				$tmp_shi = $result_shi ['name'];
				$tmp_qu = $result_qu ['name'];
			}
			echo json_encode ( $json );
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
			$condition_sheng ['parent_id'] = $id;
			$sheng = $Area->where ( $condition_sheng )->field ( 'id,name' )->order ( 'id' )->select ();var_dump($Area->fetchSql());
			$condition ['parent_id'] = $sheng [0] ['id'];
			if ($Area->where ( $condition )->find ())
				$isParent = true;
			else
				$isParent = false;
			if ($Area->where ())
				for($i = 0; $i < count ( $sheng ); $i ++) {
					$json [$i] ['name'] = $sheng [$i] ['name'];
					$json [$i] ['id'] = $sheng [$i] ['id'];
					$json [$i] ['isParent'] = $isParent;
					$json [$i] ['pId'] = $id;
				}
		} else {
			
			$Area = M ( 'Area' );
			$condition_sheng ['level_type'] = 1;
			$sheng = $Area->where ( $condition_sheng )->field ( 'id,name' )->order ( 'id' )->select ();
			for($i = 0; $i < count ( $sheng ); $i ++) {
				$json [$i] ['name'] = $sheng [$i] ['name'];
				$json [$i] ['id'] = $sheng [$i] ['id'];
				$json [$i] ['isParent'] = true;
				$json [$i] ['pId'] = 0;
			}
		}
		echo json_encode ( $json );
	}
}