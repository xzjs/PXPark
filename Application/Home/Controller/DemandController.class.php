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
     * time为正数是未来，为负数是过去，为0为当前的,比如-24就是过去24小时的数据，当前时间的就是is_success为空的数据,
     * 因为没有大数据分析，所以未来24小时的数据就是过去24小时的数据
     */
    public function get_list()
    {
        //echo C('IP');
        $time = I('param.time', 0);
        $type = I('param.type');
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
        for ($i = 0; $i < count($result); $i++) {
            $arr[$i] = array(
                "x" => $result[$i]['lon'],
                "y" => $result[$i]['lat'],
            );
        }
        $array = array(
            "data" => $arr,
        );
        echo json_encode($array);
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
        $sql = "select park_id from px_demand where car_no=" . $car_no;
        $condition['car_no'] = $car_no;
        $condition['is_success'] = array('neq', 1);
        $result_plan = M('Demand')->field('park_id,time')->where($condition)->order('time desc')->find();
        $condition1['no'] = $berth_no;
        $result_real = M('Berth')->field('park_id')->where($condition1)->find();
        if ($result_plan['park_id'] == $result_real['park_id']) {
            $condition2['park_id'] = $result_plan['park_id'];
            $condition2['time'] = $result_plan['time'];
            $data['is_success'] = 1;
            M('Demand')->where($condition2)->save($data);
            return true;
        } else {
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

    public function count_demand($lon, $lat)
    {
        $lon_floor = (floor($lon * 100000)) / 100000;
        $lon_top = $lon_floor + 0.00001;
        $lat_floor = (floor($lat * 100000)) / 100000;
        $lat_top = $lat_floor + 0.00001;


        $Demand = M('Demand');
        $codition['lon'] = array('elt', $lon_top);
        $codition['lon'] = array('egt', $lon_floor);
        $codition['lat'] = array('elt', $lat_top);
        $codition['lat'] = array('egt', $lat_floor);
        $result = $Demand->where($codition)->field('')->group('preference')->order('count(preference) desc')->select();

    }

}
