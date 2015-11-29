<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 模拟测试控制器
 * createtime:2015年11月27日 下午3:38:20
 * @author xiuge
 */
class FakeController extends BaseController
{
    /**
     * 寻找停车位fake函数
     */
    public function find()
    {
        $position = $this->get_position();
        $lon = $position['lng'];
        $lat = $position['lat'];

        $ParkController = A('Park');
        $park_list = $ParkController->getList($lon, $lat);
        $park_id = 0;
        foreach ($park_list as $park) {
            if ($park['remain'] > 0) {
                $park_id = $park['id'];
                break;
            }
        }

        $user_id = $this->get_user_id();

        $preference = rand(1, 5);

        $position = $this->get_position();
        $current_lon = $position['lng'];
        $current_lat = $position['lat'];

        $DemandController = A('Demand');
        $result = $DemandController->add($lon, $lat, $park_id, $user_id, $preference, $current_lon, $current_lat);
        echo ($result?$park_id:'失败').','.date('Y-m-d H:i:s');
    }

    /**
     * 模拟车辆驶入某停车场
     */
    public function car_in()
    {
        $condition['is_success'] = array('exp', 'is null');
        $car_list = M('Demand')->where($condition)->select();
        $rand = rand(0, count($car_list) - 1);
        $car = $car_list[$rand];//随机获取一个发布过停车请求的车
        var_dump($car);
        //var_dump($car);
        $rand = rand(0, 99);
        //2%概率未能停入规划停车场
        if ($rand > 97) {


            //98%概率停入规划停车场
        } else {
            $condition_berth['park_id'] = $car['park_id'];
            $condition_berth['is_null'] = 0;
            $berth_list = M('Berth')->where($condition_berth)->select();
            $rand = rand(0, count($berth_list) - 1);
            $berth = $berth_list[$rand];//随机获取一个空车位
            $berth['is_null'] = 1;
            M('Berth')->save($berth);

            var_dump($berth);
            $Demand = A('Demand');
            $result = $Demand->update($car['car_no'], $berth_no['no']);
            var_dump($result);
        }

    }

    public function car_out()
    {
        $Parkrecord = A('Parkrecord');
        $Parkrecord->leave();

    }

    /**
     * 模拟用户充值
     */
    public function recharge()
    {
        $user_id = $this->get_user_id();
        $money = rand(1, 100);
        $type = rand(1, 3);
        $RechargerecordController = A('Rechargerecord');
        $result = $RechargerecordController->add($user_id, $money, $type);
        echo $result ? '1,充值成功,' : '0,充值失败,';
        echo date('Y-m-d H:i:s');
    }

    /**
     * 随机获取一个用户id
     * @return mixed 用户id
     */
    private function get_user_id()
    {
        $UserController = A('User');
        $user_list = $UserController->get_list(1);
        $rand = rand(0, count($user_list)-1);
        $user=$user_list[$rand];
        if($user['Car']==null){
            $flag=true;
            while($flag){
                $car_no='鲁B'.rand(10000,99999);
                $type=rand(1,2);
                $CarController=A('Car');
                $result=$CarController->add_car_in_usrcar($user['id'],$type,$car_no);
                $flag=$result==0?false:true;
            }
        }
        $user_id = $user_list[$rand]['id'];
        return $user_id;
    }
}