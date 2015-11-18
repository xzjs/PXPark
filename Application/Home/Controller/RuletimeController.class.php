<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 验证码控制器
 * @package Home\Controller
 */
class RuletimeController extends Controller
{
    /**
     * 规则时间天剑
     * @param $start_time 开始时间
     * @param $end_time 结束时间
     * @param $fee 价格
     * @param int $type 1白天2晚上
     * @param $rule_id 规则id
     * @param int $car_type 1大车2小车
     * @return int|mixed 插入的id或者0
     */
    public function add($start_time,$end_time,$fee,$type=1,$rule_id,$car_type=1)
    {
        $ruletime = D('Ruletime');
        $data=array(
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'fee'=>$fee,
            'type'=>$type,
            'rule_id'=>$rule_id,
            'car_type'=>$car_type
        );
        if ($ruletime->create($data)) {
            $result = $ruletime->add();
            if ($result) {
                return $result;
            }
        }
        return 0;
    }

}