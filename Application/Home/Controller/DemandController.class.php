<?php

/**
 *  Created by PhpStorm.
 * User: syx
 * Date: 15/11/23
 * Time: 10:26am
 */
namespace Home\Controller;

use think\Controller;
use Think\Model;
use Org\Util\String;

class DemandController extends BaseController
{

    /**
     * 获取所有停车需求
     * @param int $time 时间
     * @param int $time_type 事件类型
     * @param int $type 数据类别
     */
    public function get_list($time = 0, $time_type = 0, $type = -1)
    {
       
        $condition = array();
        if ($time > 0) {
            $time *= -1;
        }
        $time -= 1;
        $time_str=$time_type==0?' hour':' day';
        $start_time = strtotime($time.$time_str);
        $end_time = strtotime('+1'.$time_str,$start_time);
        $condition['time'] = array('between', "$start_time,$end_time");
        switch ($type) {
            case 0:
            case 1:
                $condition['is_success'] = $type;
                break;
            case 2:
                $condition['is_success']=array('exp','is null');
                break;
            default:
                break;
        }
        $DemandModel=D('Demand');
        $result=$DemandModel->where($condition)->relation(true)->select();
        for($i=0;$i<count($result);$i++){
            $result[$i]['current_business']=$this->get_business($result[$i]['current_lon'],$result[$i]['current_lat']);
        }
        echo json_encode($result);
        /*$end_time=$start_time+24*$time_type
        if ($time == 0) {
            $condition = "is_success IS NULL";
        } else
            if ($type == null || $type == "") { //echo "ff";
                $condition = "time>UNIX_TIMESTAMP(NOW())-$time*3600";
            } else {
                if ($type == 0) {//echo "dd";
                    $condition = "time>UNIX_TIMESTAMP(NOW())-$time*3600 and is_success=0";
                } else {
                    $condition = "time>UNIX_TIMESTAMP(NOW())-$time*3600 and is_success=1";
                }
            }
        //echo "select lon ,lat from px_demand where $condition";
        $result = M()->query("select lon ,lat from px_demand where $condition");
        $arr = array();
        /*for ($i = 0; $i < count($result); $i++) {
            $arr[$i] = array(
                "x" => $result[$i]['lon'],
                "y" => $result[$i]['lat'],
            );
        }
        $array = array(
            "data" => $arr,
        );
        echo json_encode($arr);*/
    }

    /**
     * 统计当前有多少人在寻找停车场
     */
    public function count()
    {
        $num = M()->query("SELECT COUNT(id)as a FROM px_demand WHERE is_success IS NULL");
        $n = $num [0] ['a'];
        // echo "f".$n;
        $percent = ( float )$n / 100;

        echo $percent;
    }

    /**
     * 添加停车需求
     * @param $lon 经度
     * @param $lat 纬度
     * @param $park_id 停车场id
     * @param $user_id 用户id
     * @param $preference 用户偏好
     * @param $current_lon 当前经度
     * @param $current_lat 当前纬度
     * @return mixed 插入后的id或false
     */
    public function add($lon,$lat,$park_id,$user_id,$preference,$current_lon,$current_lat)
    {
        $UserModel=D('User');
        $user=$UserModel->relation(true)->find($user_id);
        $DemandModel=D('Demand');
        $data=array(
            'lon'=>$lon,
            'lat'=>$lat,
            'park_id'=>$park_id,
            'car_no'=>$user['Car'][0]['no'],
            'user_id'=>$user_id,
            'preference'=>$preference,
            'current_lon'=>$current_lon,
            'current_lat'=>$current_lat,
            'business'=>$this->get_business($lon,$lat)
        );
        if($DemandModel->create($data)){
            return $DemandModel->add();
        }else{
            throw_exception($DemandModel->getError());
        }
    }

    /**
     * 是否停车成功
     * 在用户进入车位后判断所在停车场和推荐的停车场一致，如果一致就传true，否则就传false
     */
    public function update($car_no, $berth_no)
    {
        $condition['car_no'] = $car_no;
        $condition['is_success'] = array('exp', 'is null');
        $Demand=D('Demand');
        $result_plan =$Demand->field('id,park_id,time')->where($condition)->order('time desc')->find();
        $condition1['no'] = $berth_no;
        $result_real = M('Berth')->field('id,park_id')->where($condition1)->find();
        if ($result_plan['park_id'] == $result_real['park_id']) {
            $condition2['park_id'] = $result_plan['park_id'];
            $condition2['time'] = $result_plan['time'];
            $data['is_success'] = 1;
            $result=M('Demand')->where($condition2)->save($data);
            echo "停车成功！ demand-id为".$result_plan['id'];
            return true;
        } else {
        	echo "停车失败！ 实际停车场是".$result_real['park_id'].","."规划停车场是".$result_plan['park_id'];
            return false;
        }
    }

    /**
     * 获取返回用户的选择偏好数据
     * @param unknown $user_id 用户Id
     */
    public function preference($user_id)
    {
        $Demand = M('Demand');
        $result = $Demand->where('user_id=' . $user_id)->field('preference,count(preference) as cnt')->group('preference')->order('count(preference) desc')->select();
        for ($i = 0; $i < 5; $i++) {
            if ($result[$i]['cnt']) {
                if ($result[0]['cnt'] != 0) {
                    $preference[$i] = $result[$i]['cnt'] / $result[0]['cnt'] * 100;
                }
            } else {
                $preference[$i] = 0;
            }
        }
        echo json_encode($preference);
    }

    /**
     * 根据商圈获取前往同一商圈的用户
     * @param $business 商圈名称
     */
    public function count_demand($business)
    {
    	
    	$sql="SELECT u.name,d.car_no,d.lon,d.lat,d.current_lon,d.current_lat FROM px_user AS u,px_demand AS d 
    			WHERE d.is_success IS NULL AND u.id=d.user_id AND d.business='".$business."'";
    	$result=M()->query($sql);
    	//var_dump($result);
        $json['num']=count($result);
        for($i=0;$i<count($result);$i++){
        	$json['data'][$i]['user_name']=$result[$i]['name'];
        	$json['data'][$i]['type']='本田';
        	$json['data'][$i]['car_no']=$result[$i]['car_no'];
        	$json['data'][$i]['current']['lon']=$result[$i]['current_lon'];
        	$json['data'][$i]['current']['lat']=$result[$i]['current_lat'];
        	$json['data'][$i]['destination']['lon']=$result[$i]['lon'];
        	$json['data'][$i]['destination']['lat']=$result[$i]['lat'];
        }
        echo json_encode($json);
    }

}
