<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class ParkController extends Controller
{
    public function index()
    {

    }
    /**
     * 停车场模糊搜索
     */
    public function search() {
    	$key = I ( 'param.key' );
    	$uid = I ( 'param.user_id' );
   
    	$result=M()->query("SELECT id,NAME FROM px_park WHERE user_id=$uid AND NAME LIKE '%$key%'");
    	$array=array();
    	for($i=0;$i<count($result);$i++){
    			
    		$array[$i]=array(
    				'id'=>$result[$i]['id'],
    				'name'=>$result[$i]['name'],
    					
    		);
    	}
    	echo json_encode($array);
    }

    /**
     * 停车场历史在停车辆（super）
     */
    function car_manager($sime, $etime, $type)
    {
        $result = M()->query("SELECT px_parkrecord.start_time,
				px_parkrecord.end_time,px_parkrecord.berth_id,px_parkrecord.money,px_car.no FROM px_car,
				px_parkrecordWHERE px_parkrecord.start_time>=$sime
				AND  px_parkrecord.start_time<=$etime AND px_parkrecord.type=$type 
				AND px_parkrecord.car_id=px_car.id");
        $arry = array();

        for ($i = 0; $i < count($result); $i++) {
            //$remain=$result[$i]['total_num']-$result[$i]['remain_num'];
            $typename;
            if ($result[$i]['end_time'] == 1)
                $typename = '普通收费停车厂';
            if ($result[$i]['type'] == 2)
                $typename = '路测公共停车厂';
            if ($result[$i]['type'] == 3)
                $typename = '个人车位';
            $arry[$i] = array(
                'id' => $i + 1,
                '停车场名称' => $result[$i]['parkname'],
                '注册用户名' => $result[$i]['nickname'],
                '停车场类型' => $typename,
                '停车场管理者' => $result[$i]['name'],
                '注册手机号' => $result[$i]['phone'],
                '停车场详细地址' => $result[$i]['address'],
                '停车场车位数' => $result[$i]['total_num'],

            );

        }

        return json_encode($arry);


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
     * 停车厂交易记录 (super)
     */
    function tradManager($type)
    {
        $ty = $type;
        if ($ty == 0) {

            $result = M()->query("SELECT px_park.id, px_park.name AS parkname ,px_park.type, px_park.address, px_park.total_num,px_user.name,px_user.nickname,px_user.phone FROM px_user,px_park WHERE px_park.user_id=px_user.id ");

        } else {

            $result = M()->query("SELECT px_park.id, px_park.name AS parkname ,px_park.type, px_park.address, px_park.total_num,px_user.name,px_user.nickname,px_user.phone FROM px_user,px_park WHERE px_park.user_id=px_user.id AND px_park.type=$ty ");

        }
        $arry = array();

        for ($i = 0; $i < count($result); $i++) {
            //$remain=$result[$i]['total_num']-$result[$i]['remain_num'];
            $typename;
            if ($result[$i]['type'] == 1)
                $typename = '普通收费停车厂';
            if ($result[$i]['type'] == 2)
                $typename = '路测公共停车厂';
            if ($result[$i]['type'] == 3)
                $typename = '个人车位';
            $arry[$i] = array(
                'id' => $i + 1,
                '停车场名称' => $result[$i]['parkname'],
                '注册用户名' => $result[$i]['nickname'],
                '停车场类型' => $typename,
                '停车场管理者' => $result[$i]['name'],
                '注册手机号' => $result[$i]['phone'],
                '停车场详细地址' => $result[$i]['address'],
                '停车场车位数' => $result[$i]['total_num'],

            );

        }

        return json_encode($arry);
    }

    /**
     *
     */
    function     compreManager($type)
    {
        $ty = $type;
        if ($ty == 0) {

            $result = M()->query("SELECT px_park.id, px_park.name AS parkname ,px_park.type, px_park.address, px_park.total_num,px_user.name,px_user.nickname,px_user.phone FROM px_user,px_park WHERE px_park.user_id=px_user.id ");

        } else {

            $result = M()->query("SELECT px_park.id, px_park.name AS parkname ,px_park.type, px_park.address, px_park.total_num,px_user.name,px_user.nickname,px_user.phone FROM px_user,px_park WHERE px_park.user_id=px_user.id AND px_park.type=$ty ");

        }
        $arry = array();

        for ($i = 0; $i < count($result); $i++) {
            //$remain=$result[$i]['total_num']-$result[$i]['remain_num'];
            $typename;
            if ($result[$i]['type'] == 1)
                $typename = '普通收费停车厂';
            if ($result[$i]['type'] == 2)
                $typename = '路测公共停车厂';
            if ($result[$i]['type'] == 3)
                $typename = '个人车位';
            $arry[$i] = array(
                'id' => $i + 1,
                '停车场名称' => $result[$i]['parkname'],
                '注册用户名' => $result[$i]['nickname'],
                '停车场类型' => $typename,
                '停车场管理者' => $result[$i]['name'],
                '注册手机号' => $result[$i]['phone'],
                '停车场详细地址' => $result[$i]['address'],
                '停车场车位数' => $result[$i]['total_num'],

            );

        }

        return json_encode($arry);
    }

    /**
     *停车厂实时状态
     */
    function chewei_status($type)
    {

        $ty = $type;
        if ($type == 0) {

            $result = M()->query("SELECT NAME ,img, total_num,remain_num FROM px_park  ");

        } else {

            $result = M()->query("SELECT NAME ,img, total_num,remain_num FROM px_park where type=$ty ");

        }
        $arry = array();

        for ($i = 0; $i < count($result); $i++) {
            $remain = $result[$i]['total_num'] - $result[$i]['remain_num'];

            $arry[$i] = array(
                'id' => $i,
                'title' => $result[$i]['name'],
                'img' => '/PXPark/Public/image/ParkImg/' . $result[$i]['img'],
                'usingNum' => $remain,
                'sumNum' => $result[$i]['total_num'],

            );

        }

        return json_encode($arry);

    }


    /**
     *
     */
    function coming_zhishu_line()
    {
        $park_id = 1;
        $now = strtotime("now ");
        $cout_start;
        $cut_time = $now - 24 * 60 * 60;
        $now_h = date("H", $now) + 1;
        $p = 0;
        $n = $now_h - 1;
        for ($h = 23; $h >= 0; $h--) {

            if ($n - $p >= 0)
                $ti[$h] = $n - $p;
            else
                $ti[$h] = $n - $p + 24;
            $p++;

        }
        for ($j = 0; $j < 24; $j++) {
            $time[$j] = $ti[$j] . ":00";
            //echo "t:".$time[$j]."<br>";
        }
        for ($h = 0; $h < 24; $h++) {
            $sum [$h] = 0;
        }
        for ($h = 0; $h < 24; $h++) {
            $sum_num[$h] = 0;
        }
        for ($h = 0; $h < 24; $h++) {
            $value[$h] = 0;
        }
        $result = M()->query("SELECT num ,time FROM px_target WHERE
						$cut_time<=time  AND park_id=$park_id ORDER BY TIME ");
        //echo "ctime".$cut_time;
        for ($j = 0; $j < count($result); $j++) {
            $shour = date("H", $result [$j] ['time']);
            if ($shour > $now_h)
                $ns = $shour - $now_h;
            else {
                $ns = $shour - $now_h + 24;
            }
            //	echo "ns:".$ns."<br>";
            $sum_num[$ns]++;
            //	echo "sum_mum:".$sum_num[$ns]."<br>";

            $sum[$ns] = $sum[$ns] + $result [$j] ['num'];
            //	echo "sum:".$sum[$ns]."<br>";
        }
        //echo "num:".count($result)."<br>";
        for ($h = 0; $h < 24; $h++) {
            $value[$h] = round($sum[$h] / $sum_num[$h], 2) * 10;
            //echo "valeu:".$value[$h]."<br>";
        }
        $array = array(
            "time" => $time,
            "value" => $value,
        );
        //echo "ww".json_encode($array);
        return json_encode($array);
    }

    /**
     * 获取日期段内停车指数
     *
     */
    function zhishu_line($stime, $etime)
    {
        $park_id = 1;
        $s = $stime;
        $e = $etime;
        $result = M()->query("SELECT num ,time FROM px_target WHERE
				$stime<=time AND time<=$etime+86400 AND park_id=$park_id ORDER BY TIME ");

        $i = 0;
        $num = 0;
        $time;
        $value;
        $sum;
        $sum_num;
        while ($s <= $e) {

            $time[$i] = date("m-d", $s);
            $s = $s + 86400;

            $i++;
            $num++;
        }
        //	echo "nuddm:".$num."<br>";
        for ($h = 0; $h < $num; $h++) {
            $sum [$h] = 0;
        }
        for ($h = 0; $h < $num; $h++) {
            $sum_num[$h] = 0;
        }
        for ($h = 0; $h < $num; $h++) {
            $value[$h] = 0;
        }
        for ($j = 0; $j < count($result); $j++) {
            $d = date('Y-m-d', $result [$j] ['time']);
            //echo "d:".$d."<br>";
            $ns = (int)((strtotime($d) - $stime) / 86400);
            //echo "ns:".$ns."<br>";
            $sum_num[$ns]++;
            //	echo "sum_mum:".$sum_num[$ns]."<br>";

            $sum[$ns] = $sum[$ns] + $result [$j] ['num'];
            //	echo "sum:".$sum[$ns]."<br>";
        }
        //echo "num:".count($result)."<br>";
        for ($h = 0; $h < $num; $h++) {
            $value[$h] = round($sum[$h] / $sum_num[$h], 2) * 10;
            //	echo "valeu:".$value[$h]."<br>";
        }
        $array = array(
            "time" => $time,
            "value" => $value,
        );
        //echo "ww".json_encode($array);
        return json_encode($array);
    }

    /**
     * 获取各类停车场数量
     */
    function get_parks_num()
    {
        $result = M()->query("SELECT id ,TYPE FROM px_park ");
        $pu_n = 0;
        $lu_n = 0;
        $ge_n = 0;
        $ch_n = 0;
        for ($i = 0; $i < count($result); $i++) {
            if ($result[$i]['type'] == 1) {
                $pu_n++;
            }
            if ($result[$i]['type'] == 2) {
                $lu_n++;
            }
            if ($result[$i]['type'] == 3) {
                $ge_n++;
            }
        }
        $array = array(
            "pu" => $pu_n,
            "lu" => $lu_n,
            "ge" => $ge_n,
            "ch" => $ch_n,
        );
        return $array;
    }

    /**
     * 获取停车场列表或者指定停车场
     */
    public function status()
    {
        $park = M('Park');
        if (I('param.type', 0) != 0) {
            $condition['type'] = I('param.type');
        }
        if (I('param.park_id', 0) != 0) {
            $condition['id'] = I('param.park_id');
        }
        $result = $park->where($condition)->field('name,total_num,remain_num,img')->select();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['img'] = C('IP') . __ROOT__ . C('PARK_IMG_PATH') . $result[$i]['img'];//图片链接url
        }
        $data['data'] = $result;
        echo json_encode($data);
    }

    /**
     * 添加停车场信息
     */
    public function add()
    {
        $park = D('Park');
        if ($park->create()) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = '.' . C('PARK_IMG_PATH'); // 设置附件上传根目录
            $upload->autoSub = false;
            // 上传文件
            $info = $upload->upload();
            /*$park->img=$info[0]['savename'];
            $park->licence_img=$info[1]['savename'];
            $park->id_img=$info[2]['savename'];*/
            $park->img = $info['img']['savename'];
            $park->remain_num = $park->total_num;
            $park->user_id = $_SESSION('name');
            $result = $park->add();
            if ($result) {
                $this->success('数据添加成功！');//添加成功
            } else {
                $this->error('数据添加错误！');//添加失败
            }
        } else {
            $this->error($park->getError());//验证失败
        }
    }


    /**
     * 根据经纬度获取周围一定距离的有空停车位的停车场列表
     * @param int $lon 经度
     * @param int $lat 纬度
     * @param int $num 所需停车场数目
     * @return array 目地停车场列表
     */
    public function getList($lon = 0, $lat = 0)
    {
        $num=1000;
        $distance_lon = 180;//经纬度误差，保证给用户的停车场是用户一定距离范围内的
        $distance_lat = 180;
        $condition ['lon'] = array(
            array(
                'gt',
                $lon - $distance_lon
            ),
            array(
                'lt',
                $lon + $distance_lon
            )
        );
        $condition ['lat'] = array(
            array(
                'gt',
                $lat - $distance_lat
            ),
            array(
                'lt',
                $lat + $distance_lat
            )
        );
        $condition['remain_num']=array('gt',0);
        $park = M('Park');
        $result = $park->where($condition)->field('id,name,lon,lat,price,remain_num as remain,total_num as total,img')->select();
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['img'] = C('PARK_IMG_PATH') . $result[$i]['img'];//图片链接url
        }
        $distance = array();
        for ($i = 0; $i < count($result); $i++) {
            $distance [] = $this->getDistance($lat, $lon, $result [$i] ['lat'], $result [$i] ['lon']);
        }
        array_multisort($distance, SORT_ASC, $result); // 按距离排序

        return  array_slice($result,0,$num);
    }

    /**
     * 获取停车场详细信息
     * @param int $park_id 停车场id
     * @return mixed 停车场详细信息
     */
    public function getDetail($park_id = 0)
    {
        $park = D('Park');
        /*$condition ['id'] = $park_id;
        $result = $park->where ( $condition )->field ( 'id,name,lon,lat,price,remain_num as remain,total_num as total,
                type,address,img' )->select ();
        return $result;*/
        return $park->relation(true)->find($park_id);

    }

    /**
     * 获取停车记录
     *
     * @param number $use_id
     *            用户id
     */
    public function getRecord($user_id = 0)
    {
        $Model = new Model ();
        $sql = 'select c.name as park_name,d.no as car_no,a.start_time,a.end_time,a.money from px_parkrecord as a,
				px_user_car as b,px_park as c,px_car as d where a.car_id=b.car_id and b.user_id=' . $user_id . ' 
						and b.status=1 and a.park_id=c.id and b.car_id=d.id order by a.id';
        $result = $Model->query($sql);
        return $result;
    }

    /**
     * 根据两点间的经纬度计算距离
     *
     * @param float $lat
     *            纬度值
     * @param float $lng
     *            经度值
     */
    function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; // approximate radius of earth in meters

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    /**
     * 给城市所增加停车场的接口
     */
    public function add_temp_test()
    {
        $Park = D('Park');
        $this->assign('list', $Park->select());
        $this->show();
    }

    /**
     * 城市所需要的修改停车场剩余车位数的信息
     */
    public function update_remain()
    {
        $Park = D('Park');
        $data = array(
            'id' => 3,
            'remain_num' => (170 - I('post.remain'))
        );
        if ($Park->save($data)) {
            $this->success('修改成功');
        } else {
            $this->error($Park->getDbError());
        }
    }

    /**
     * 修改剩余车位数页面
     */
    public function update_remain_temp()
    {
        $Park = D('Park');
        $data = $Park->find(3);
        $this->assign('remain', $data['total_num'] - $data['remain_num']);
        $this->show();
    }
}