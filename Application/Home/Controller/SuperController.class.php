<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/2
 * Time: 下午10:26
 */
namespace Home\Controller;

use Think\Controller;
use Think\Model;

/**
 * 超级管理员控制器
 * @package Home\Controller
 */
class SuperController extends BaseController
{

    /*public function __construct()
    {
        parent::__construct();
        if ($_SESSION['type'] != 4) {
            $this->error('权限不够,请登录', U('Index/index'));
        }
    }*/

    /**
     * 车辆管理
     */
    public function carManager()
    {
        if (I('param.park_id', 0) != 0) {
            $condition = ' where px_parkrecord.park_id=' . I('param.park_id');
        } else {
            if (I('param.user_id', 0) == 0)
                $user_id = $_SESSION['user']['user_id'];
            else
                $user_id = I('param.user_id', 0);
            $condition = ',px_park WHERE px_parkrecord.park_id=px_park.id AND px_park.user_id=' . $user_id;
        }

        if (I('param.type', 0) != 0) {
            $condition1 .= " and px_car.type=" . I('param.type');
        }
        if (I('param.flag', 0) != 0) {
            $condition1 .= " and px_parkrecord.end_time is null";
        }
        if ((I('param.start_time', 0) != 0) && (I('param.end_time', 0) != 0)) {
            $in_time = strtotime(I('param.start_time'));
            $out_time = strtotime(I('param.end_time'));
            $condition1 .= ' and px_parkrecord.start_time between ' . $in_time . ' and ' . $out_time;
        }
        if ((I('param.page', 0) != 0) && (I('param.num', 0) != 0)) {
            $condition1 .= "order by px_parkrecord.start_time desc" . ' limit ' . $page * ($num - 1) . ',' . $page;
        }
        $Model = new Model ();
        $sql = "select px_car.no as car_no,px_car.type,px_parkrecord.end_time,px_parkrecord.money,px_parkrecord.start_time,px_berth.no as park_no,unix_timestamp(now())-px_parkrecord.start_time as time,px_user.member_id
				from px_parkrecord,px_berth,px_user,px_car,px_user_car " . $condition . " and px_car.id=px_parkrecord.car_id and px_parkrecord.berth_id=px_berth.id
				and px_car.id=px_user_car.car_id and px_user.id=px_user_car.user_id and px_berth.is_null=1 AND px_parkrecord.end_time IS null order by px_parkrecord.start_time desc";

        $sql1 = "select px_car.no as car_no,px_car.type,px_parkrecord.end_time,px_parkrecord.money,px_parkrecord.start_time,px_berth.no as park_no,unix_timestamp(now())-px_parkrecord.start_time as time,px_user.member_id
				from px_parkrecord,px_berth,px_user,px_car,px_user_car " . $condition . " and px_car.id=px_parkrecord.car_id and px_parkrecord.berth_id=px_berth.id
				and px_car.id=px_user_car.car_id and px_user.id=px_user_car.user_id " . $condition1;
        if (!$condition1)
            $result = $Model->query($sql);
        else
            $result = $Model->query($sql1);
        $json_array = array("total" => 0, "rows" => array(), "in_num" => 0, "finish_num" => 0, "money" => 0);//easyUI表格的固定格式
        for ($i = 0; $i < count($result); $i++) {
            if (($result[$i]['start_time'] >= $in_time) && ($result[$i]['start_time'] <= $out_time))
                $json['in_num']++;
            if (($result[$i]['money']))
                $json['finish_num']++;
            $json['money'] += $result[$i]['money'];
            $json['rows'][$i]['car_no'] = $result[$i]['car_no'];
            if ($result[$i]['type'] == 1) {
                $json['rows'][$i]['type'] = "小型车";
            } elseif ($result[$i]['type'] == 2) {
                $json['rows'][$i]['type'] = "大型车";
            }
            $json['rows'][$i]['start_time'] = date("Y-m-d h:i:sa", $result[$i]['start_time']);
            if (!$result[$i]['end_time']) {
                $json['rows'][$i]['end_time'] = "";
                $json['rows'][$i]['time'] = $this->time_tran($result[$i]['time']);
            } else {
                $json['rows'][$i]['end_time'] = date("Y-m-d h:i:sa", $result[$i]['end_time']);
                $time_num = $result[$i]['end_time'] - $result[$i]['start_time'];
                $json['rows'][$i]['time'] = $this->time_tran($time_num);
            }
            $json['rows'][$i]['park_no'] = $result[$i]['park_no'];
            $json['rows'][$i]['cost'] = $result[$i]['money'];
            switch ($result[$i]['member_id']) {
                case 1:
                    $json['rows'][$i]['member_id'] = '普通会员';
                    break;
                case 2:
                    $json['rows'][$i]['member_id'] = '白银会员';
                    break;
                case 3:
                    $json['rows'][$i]['member_id'] = '黄金会员';
                    break;
                default:
                    $json['rows'][$i]['member_id'] = '';
                    break;
            }
        }
        $json['total'] = count($json['rows']);
        if (!$condition1) {
            $this->assign('total', $json['total']);
            $this->assign('info', json_encode($json));
            $this->display();
        } else
            echo json_encode($json);
    }

    /**
     * 停车类型收益与入口流量统计
     *
     */
    function statisticInfo()
    {
        $user_id = 1;
        //$Model = new Model ();
        $last_month = strtotime("last Month") + mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")) - time();
        //var_dump(date("Y-m-d h:i:sa",$last_month));
        $sql_small = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE c.type=1 AND b.id=" . $user_id . " AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>" . $last_month . " GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d')";
        $result_small = M()->query($sql_small);

        $sql_big = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE c.type=2 AND b.id=" . $user_id . " AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>" . $last_month . " GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d') ORDER BY
			FROM_UNIXTIME(a.start_time,'%m-%d') DESC LIMIT 0,30";
        $result_big = M()->query($sql_big);

        for ($i = 0; $i < 30; $i++) {
            $date = date("m-d", strtotime("-" . (29 - $i) . " Days"));
            $colum_chart[$i]['date'] = $date;
            for ($j = 0; $j < count($result_small); $j++) {
                if ($result_small[$j]['atime'] == $date) {
                    $colum_chart[$i]['small'] = $result_small[$j]['cnt'];
                    $colum_chart[$i]['money'] = $result_small[$j]['money'];
                }
            }
            for ($j = 0; $j < count($result_big); $j++) {
                if ($result_big[$j]['atime'] == $date) {
                    $colum_chart[$i]['big'] = $result_big[$j]['cnt'];
                    $colum_chart[$i]['money'] += $result_big[$j]['money'];
                }
            }
        }
        //var_dump($colum_chart);

        $sql_pie = "SELECT COUNT(*) cnt, c.type,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d WHERE b.id=" . $user_id . " AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>" . $last_month . " GROUP BY c.type,a.money";
        $result_pie = M()->query($sql_pie);
        //var_dump($result_pie);
        $pie = array("small" => 0, "small_free" => 0, "big" => 0, "big_free" => 0);
        for ($i = 0; $i < count($result_pie); $i++) {
            if (($result_pie[$i]['money'] != 0) && ($result_pie[$i]['type'] == 1))
                $pie['small'] += $result_pie[$i]['cnt'];
            elseif (($result_pie[$i]['money'] == 0) && ($result_pie[$i]['type'] == 1))
                $pie['small_free'] += $result_pie[$i]['cnt'];
            elseif (($result_pie[$i]['money'] != 0) && ($result_pie[$i]['type'] == 2))
                $pie['big'] += $result_pie[$i]['cnt'];
            elseif (($result_pie[$i]['money'] == 0) && ($result_pie[$i]['type'] == 2))
                $pie['big_free'] += $result_pie[$i]['cnt'];
        }
        //var_dump($pie);

        $last_day = strtotime("-1 Day") + mktime(date("H") + 1, 0, 0, date("m"), date("d"), date("Y")) - time();
        //var_dump(date("Y-m-d h:i:sa",$last_day));
        $sql_line = "SELECT FROM_UNIXTIME(a.start_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE b.id=" . $user_id . " AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>" . $last_day . " GROUP  BY FROM_UNIXTIME(a.start_time,'%H')";
        $result_line = M()->query($sql_line);
        $sql_line = "SELECT FROM_UNIXTIME(a.end_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE b.id=" . $user_id . " AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.end_time>" . $last_day . " GROUP  BY FROM_UNIXTIME(a.end_time,'%H')";
        $result_line_end = M()->query($sql_line);
        //var_dump($result_line);
        for ($i = 0; $i < 24; $i++) {
            $time = date("H", strtotime("-" . (23 - $i) . " Hours"));
            $line_chart[$i]['time'] = $time;
            for ($j = 0; $j < count($result_line); $j++) {
                if ($result_line[$j]['atime'] == $time) {
                    $line_chart[$i]['cnt'] = $result_line[$j]['cnt'];
                }
            }
            for ($j = 0; $j < count($result_line_end); $j++) {
                if ($result_line_end[$j]['atime'] == $time) {
                    $line_chart[$i]['cnt_end'] = $result_line_end[$j]['cnt'];
                }
            }
        }
        //var_dump($line_chart);
        $this->assign('columInfo', json_encode($colum_chart));
        $this->assign('pieInfo', json_encode($pie));
        $this->assign('lineInfo', json_encode($line_chart));
        $this->display();
    }

    /**
     * 获取单个用户停车历史记录给前端用
     */
    function getparkrecord()
    {
        $user = A('User');

        $result = $user->getparkrecord(I('post.u_id'));
        //echo I('post.u_id');
        echo $result;
    }

    /**
     * 获取所有普通用户信息
     * 目前荣誉等级为空
     */
    function persons_info()
    {
        $user = A('User');
        $result = $user->persons_info();
        echo $result;
    }

    /**
     * 停车厂综合管控
     */
    function cManager()
    {
        $park = A('Park');
        $result = $park->compreManager(I('post.type'));
        echo $result;
    }

    /**
     * 停车状态
     */
    function chewei_status()
    {
        $park = A('Park');
        //echo "suer".$
        $result = $park->chewei_status(I('post.type'));
        //echo "rr".I('post.type');
        echo $result;
    }

    /**
     * 未来24停车指数
     */
    function  coming_zhishu_line()
    {
        $park = A('Park');
        $result = $park->coming_zhishu_line();
        echo $result;
    }

    /**
     * 获取日期段内停车指数
     */
    function zhishu_line()
    {
        $park = A('Park');
        $stime = strtotime(I('post.stime'));
        $etime = strtotime(I('post.etime'));
        $result = $park->zhishu_line($stime, $etime);

        echo $result;
        //	echo "hah";
    }

    /**
     * 获取个停车场个数
     */
    function pie_parks()
    {

        $park = A('Park');
        $result = $park->get_parks_num();

        //echo $array;
        echo json_encode($result);

    }

    public function tradManager()
    {
        $type = 0;
        $Park = D('Park');
        $condition = $type ? array(
            'type' => $type
        ) : array();
        $car_id_array = $Park->where($condition)->getField('id', true);
        $ParkrecordController = A('Parkrecord');
        $parkrecord_array = $ParkrecordController->complete_list($car_id_array);
        $arry = array();

        for ($i = 0; $i < count($parkrecord_array); $i++) {
            $Car = D('Car');
            $parkrecord_array [$i] ['car_user'] = $Car->relation(true)->find($parkrecord_array [$i] ['car_id']);
            $parkrecord_array [$i] ['park_user'] = $Park->relation(true)->find($parkrecord_array [$i] ['park_id']);
            $arry [$i] = array(
                "carNo" => $parkrecord_array [$i] ['car_user'] ['no'],
                "name" => $parkrecord_array [$i] ['car_user'] ['User'] ['nickename'],
                "telphone" => $parkrecord_array [$i] ['car_user'] ['User'] ['phone'],
                "parking" => $parkrecord_array [$i] ['park_user'] ['name'],
                "position" => $parkrecord_array [$i] ['berth_id'],
                "regTelphone" => $parkrecord_array [$i] ['park_user'] ['User'] ['phone'],
                "inTime" => $parkrecord_array [$i] ['start_time'] == '' ? '' : date('Y.m.d H:i:s', $parkrecord_array [$i] ['start_time']),
                "outTime" => $parkrecord_array [$i] ['end_time'] == '' ? '' : date('Y.m.d H:i:s', $parkrecord_array [$i] ['end_time']),
                "sumConsume" => $parkrecord_array [$i] ['money']
            );
            // echo $parkrecord_array;
        }
    }

    /**
     * 获取天气情况
     */
    public function get_weather()
    {
        $Weather = A('Weather');
        $html_str = $Weather->get_weather(I('param.city_code'));
        echo $html_str;
    }


    /**
     * 获取地区列表
     */
    public function get_area_list()
    {
        $Area = A('Area');
        $result = $Area->get_area_list(I('param.id'));
        echo $result;
    }


    private function time_tran($the_time)
    {

        $dur = $the_time;
        if ($dur < 0) {
            return $the_time;
        } else {
            if ($dur < 60) {
                return $dur . '秒';
            } else {
                if ($dur < 3600) {
                    return floor($dur / 60) . '分钟';
                } else {
                    if ($dur < 86400) {
                        return floor($dur / 3600) . '小时';
                    } else {
                        if ($dur < 259200000) { //3天内
                            return floor($dur / 86400) . '天';
                        } else {
                            return $the_time;
                        }
                    }
                }
            }
        }
    }
}